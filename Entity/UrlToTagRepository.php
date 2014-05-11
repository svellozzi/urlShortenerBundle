<?php
namespace Vellozzi\UrlShortenerBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Vellozzi\UrlShortenerBundle\Exception\InvalidParameterException;


/**
 * UrlToTagRepository
 *
 * This class centralizes common databases requests with entity UrlToTag.
 */
class UrlToTagRepository extends EntityRepository
{
    /**
     * It count   the saved shortened urls
     * @return int
     */
    public function findNbShortenedUrls()
    {
        $em = $this->_em;
        $query = $em->createQuery('SELECT COUNT(u.id) FROM VellozziUrlShortenerBundle:UrlToTag u');
        $count = $query->getSingleScalarResult();
        
        return $count;
    }  
    public function findAllShortenedUrls($pageNumber=1,$nbItemByPage=100)
    {
        if (false == $this->isPositiveInteger($pageNumber)) {
            throw new InvalidParameterException("pageNumber $pageNumber is not a positive integer");
        }
        if (false == $this->isPositiveInteger($nbItemByPage)) {
            throw new InvalidParameterException("nbItemByPage $nbItemByPage is not a positive integer");
        }
        $offset = (int) (($pageNumber - 1) * $nbItemByPage);
        $em = $this->_em;

        $qb = $em->createQueryBuilder();

        $qb->select('urlsShortened')
           ->from('VellozziUrlShortenerBundle:UrlToTag', 'urlsShortened')
           ->setFirstResult($offset)
           ->setMaxResults($nbItemByPage);

        $query = $qb->getQuery();
        $urlsShortened = $query->getResult();
        if (is_array($urlsShortened) == true
            && count($urlsShortened) > 0)
        {
            return $urlsShortened;
        } else {
            return false;
        }
    }
    
    public function getQuerySearch($searchtTxt)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->select('urlsShortened')
           ->from('VellozziUrlShortenerBundle:UrlToTag', 'urlsShortened');
        if (strlen(trim($searchtTxt)) > 0) {
            $tokens = explode(' ', $searchtTxt);
            foreach($tokens as  $token) {
                if (strlen(trim($token)) > 0) {
                    $qb->orWhere($qb->expr()->like('urlsShortened.url', $qb->expr()->literal("%$token%")));
                    $qb->orWhere($qb->expr()->like('urlsShortened.tag', $qb->expr()->literal("%$token%")));
                }
            }
        }

        $query = $qb->getQuery();
        
        return $query;
    }
    protected function isPositiveInteger($value)
    {
        return is_int($value) && $value>0;
    }
    public function findAllShortenedUrlsHavingReachedMaxUse()
    {
      $em = $this->_em;

      $qb = $em->createQueryBuilder();
    
      $qb->select('urlsShortened')
         ->from('VellozziUrlShortenerBundle:UrlToTag', 'urlsShortened')
         ->where('urlsShortened.maxAllowedUse > 0')
         ->andWhere('urlsShortened.nbUsed >= urlsShortened.maxAllowedUse');
    
      $query = $qb->getQuery();
      $urlsShortened = $query->getResult();
      if (is_array($urlsShortened) == true
          && count($urlsShortened) > 0)
      {
          return $this->convertEntitiesToListOfIds($urlsShortened);
      } else {
          return false;
      }
    }
    public function findAllShortenedUrlsHavingExpiredLifetime()
    {
      $em = $this->_em;
      $today = new \DateTime("now");
      $qb = $em->createQueryBuilder();
    

      $qb->select('urlsShortened')
         ->from('VellozziUrlShortenerBundle:UrlToTag', 'urlsShortened')
         //->where('urlsShortened.expireAt > 0')
         ->where('urlsShortened.expireAt <= :now')
         ->setParameter('now', $today)
         ->andWhere('urlsShortened.expireAt is not null')     
         ->expr()->isNotNull('urlsShortened.expireAt');
                 
      $query = $qb->getQuery();
      $urlsShortened = $query->getResult();
      if (is_array($urlsShortened) == true
          && count($urlsShortened) > 0)
      {
          
          return $this->convertEntitiesToListOfIds($urlsShortened);
      } else {
          return false;
      }
    }
    public function findAllUnusedShortenedUrls(\DateTime $olderThan = null)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        if ($olderThan instanceof \DateTime) {
            //do nothing
        } else {
            $olderThan = new \DateTime("now"); 
            $olderThan->modify('-1 month');
        }
        $qb->select('urlsShortened')
          ->from('VellozziUrlShortenerBundle:UrlToTag', 'urlsShortened')
          ->where('urlsShortened.createdAt <= :olderThan')
          ->setParameter('olderThan', $olderThan)
          ->andWhere('urlsShortened.lastUsedAt is  null')     
          ->expr()->isNull('urlsShortened.lastUsedAt');

        $query = $qb->getQuery();
        $urlsShortened = $query->getResult();
        if (is_array($urlsShortened) == true
           && count($urlsShortened) > 0)
        {

         return $this->convertEntitiesToListOfIds($urlsShortened);
        } else {
         return false;
        }
    }
    
    public function massiveDelete($ids)
    {
        if (is_array($ids)) {
            $em = $this->_em;
            $dql= "DELETE VellozziUrlShortenerBundle:UrlToTag u WHERE u.id in (".implode(',', $ids).")";
            $query = $em->createQuery($dql);
            $res = $query->getResult();
            return $res == count($ids);
         
        } else{
            return false;
        }
        
    }
    protected function convertEntitiesToListOfIds($urlsShorteneds)
    {
        $ret = array();
        if (is_array($urlsShorteneds)) {
            foreach($urlsShorteneds as $anUrlsShortened) {
                if ($anUrlsShortened instanceof UrlToTag) {
                    $ret[] = (int) $anUrlsShortened->getId();
                }   
            }
        }
        return $ret;
    }
}