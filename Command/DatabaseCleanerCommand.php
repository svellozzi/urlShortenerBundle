<?php
namespace Vellozzi\UrlShortenerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
/**
 * DatabaseCleaner removes unvalid entries 
 * unvalid antries are :
 *  - rows having reached maxuse
 *  - rows having expired lifetime
 *  - rows never used
 * @todo 
 *  - code comments
 * @author Sebastien Vellozzi
 */
class DatabaseCleanerCommand extends ContainerAwareCommand 
{
    private $listIdToRemove = array();
    private $target  = null;
    private $isDryrun = false;
    private $olderThan = null;
    private $parcelSize;
    const TARGET_MAXUSE = 'maxuse';
    const TARGET_ALL = 'all';
    const TARGET_LIFETIME = 'lifetime';
    const TARGET_UNUSED= 'unused';
    
    protected function configure()
    {
        $this
            ->setName('vellozzi:urlshortener:databaseCleaner')
            ->setDescription('cleaning database of url shortened unvalid. ie :  max use reached, lifetime finished')
            ->addOption('dryrun', null, InputOption::VALUE_NONE, 'dryrun mode')
            ->addOption('target', null, InputOption::VALUE_OPTIONAL, 'maxUse|lifetime|all', 'all')
            ->addOption('parcelSize', null, InputOption::VALUE_OPTIONAL, 'parcel size for deleting', '100')
            ->addOption('olderThan', null, InputOption::VALUE_OPTIONAL, 'for deleting unused  shortened url defaut 1 montholder or more (format YYYY-MM-DD)', null)
            ->addOption('memoryLimit', null, InputOption::VALUE_OPTIONAL, 'allowed memory in M  for the comman', '32')    
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        try {
            $this->init($input);
            $output->writeln("retieving items to remove");
            $this->retrieveItemsToDelete();
            $nb = count($this->listIdToRemove);
            if ($nb == 0) {
                $output->writeln("Nothing to remove");
            } else {
                $output->writeln("$nb items to remove");
                $parcels = array_chunk($this->listIdToRemove, $this->parcelSize);
                $nbParcels =  count($parcels);
                $output->writeln("$nbParcels parcel(s) to do");
                $progress = $this->getHelperSet()->get('progress');
                $progress->setFormat(ProgressHelper::FORMAT_VERBOSE);
                $progress->start($output, $nbParcels);
                foreach($parcels as $aParcel) {
                    if (false === $this->doCleaning($aParcel)) {
                        echo "something goes wrong witht he folling ids :\n";
                        print_r($aParcel);
                    }
                    $progress->advance();
                }
                $progress->finish();
            }
        } catch (LogicException $ex) {
            $output->writeln($ex->getMessage());
        }

    }
    
    protected function init(InputInterface $input)
    {
         if ($input->getOption('dryrun')) {
             $this->isDryrun = true;
         }
         $target = strtolower($input->getOption('target'));
         $validTargets = array(self::TARGET_MAXUSE, self::TARGET_ALL, self::TARGET_LIFETIME, self::TARGET_UNUSED);
         if (in_array($target, $validTargets)) {
             $this->target = $target;
         } else {
             throw new \LogicException('target is not valid');
         }
         $parcelSize = (int) $input->getOption('parcelSize');
         if ($parcelSize>0) {
             $this->parcelSize = $parcelSize;
         } else {
             throw new \LogicException('parcelSize must be a positive integer');
         }
         $olderThan = strtolower($input->getOption('olderThan'));
         if (!empty($olderThan)) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $olderThan )) {
                $this->olderThan = new \dateTime($olderThan.' 00:00:00');
            } else {
                throw new \LogicException('olderTan is invalid (format YYYY-MM-DD)');
            }
         }
         $memoryLimit = (int) $input->getOption('memoryLimit');
         if ($memoryLimit>0) {
             $this->parcelSize = $parcelSize;
         } else {
             throw new \LogicException('memory limit must be a positive integer');
         }
         ini_set('memory_limit',$memoryLimit.'M');
         
    }
    protected function retrieveItemsToDelete()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $ret = false; 
        if (self::TARGET_ALL == $this->target
            || self::TARGET_MAXUSE == $this->target) {
            $ret = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllShortenedUrlsHavingReachedMaxUse();
        } 
        if (is_array($ret)&& count($ret)) {
            $this->listIdToRemove = array_merge($this->listIdToRemove,$ret);
        }
        $ret = false;
        if (self::TARGET_ALL == $this->target
            || self::TARGET_LIFETIME == $this->target) {
            $ret = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllShortenedUrlsHavingExpiredLifetime();
        }
        if (is_array($ret)&& count($ret)) {
            $this->listIdToRemove = array_merge($this->listIdToRemove,$ret);
        }
        if (self::TARGET_ALL == $this->target
            || self::TARGET_UNUSED == $this->target) {
            if ($this->olderThan instanceof \DateTime) {
                $ret = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllUnusedShortenedUrls($this->olderThan);
            } else {
                $ret = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findAllUnusedShortenedUrls();
            }
        }
        if (is_array($ret)&& count($ret)) {
            $this->listIdToRemove = array_merge($this->listIdToRemove,$ret);
        }
        
        if (is_array($this->listIdToRemove ) && count($this->listIdToRemove)) {
            $this->listIdToRemove = array_unique($this->listIdToRemove);
        }
    }

    protected function doCleaning($listIdToRemove) {
         $manager = $this->getContainer()->get('vellozzi_urlshortener.urlshortener_manager');
         $ret = true;
         if (false === $this->isDryRun()) {
            $em = $this->getContainer()->get('doctrine')->getEntityManager();
            $ret = $em->getRepository('VellozziUrlShortenerBundle:UrlToTag')->massiveDelete($listIdToRemove);
         }

         return $ret;
    }
    protected function isDryRun() {
       return $this->isDryrun; 
    }
}
