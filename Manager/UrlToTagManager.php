<?php
namespace Vellozzi\UrlShortenerBundle\Manager;
use Doctrine\ORM\EntityManager;
use Vellozzi\UrlShortenerBundle\Entity\UrlToTag;
use Vellozzi\UrlShortenerBundle\Model\UrlShortener;
use Vellozzi\UrlShortenerBundle\Model\TagGenerator;
use Vellozzi\UrlShortenerBundle\Model\BaseModel;
/**
 * Description of UrlToTagManager
 *
 * @author seb
 */
class UrlToTagManager extends BaseModel
{
    /**
    * @var EntityManager
    */
    protected $em;
    public function __construct()
    {
    }

    public function loadFromId($id)
    {
        if (is_int($id) && $id>0) {
            $entity = $this->getRepository()->find($id);
            if ($entity instanceof UrlToTag) {
                return $this->createUrlShortenerFromEntity($entity);
            }
        }

        return false;
    }
    public function loadFromTag($tag)
    {
        if (strlen($tag)>0) {
            $entities = $this->getRepository()->findBy(array('tag' => $tag));
            foreach ($entities as $entity) {
                if ($entity->getTag() == $tag) {
                      return $this->createUrlShortenerFromEntity($entity);
                }
            }
        }

        return false;
    }

    public function save(UrlShortener $u)
    {
        if ($u->getId()>0) {
            $entity = $this->getRepository()->find($u->getId());
        } else {
            $entity = new UrlToTag();
        }
        if ($entity instanceof UrlToTag) {
            $entity->setMaxAllowedUse($u->getMaxAllowedUse());
            $entity->setTag($u->getShortTag());
            if (!is_null($u->getExpireAt())) {
                $entity->setExpireAt($u->getExpireAt());
            }
            $entity->setNbUsed($u->getNbUsed());
            $entity->setUpdatedAt($u->getUpdatedAt());
            $entity->setCreatedAt($u->getCreatedAt());
            if (!is_null($u->getLastUsedAt()) && is_object($u->getLastUsedAt())) {
                $entity->setLastUsedAt($u->getLastUsedAt());
            }
            $entity->setUrl($u->getUrl());
            $this->em->persist($entity);
            $this->em->flush();
            $u->setId($entity->getId());

            return true;
        }

      return false;
    }
    public function createUrlShortenerFromEntity(UrlToTag $entity)
    {
        $urlShortener = new UrlShortener();
        $urlShortener->setId($entity->getId());
        $urlShortener->setMaxAllowedUse($entity->getMaxAllowedUse());
        $urlShortener->setShortTag($entity->getTag());
        $urlShortener->setExpireAt($entity->getExpireAt());
        $urlShortener->setNbUsed($entity->getNbUsed());
        $urlShortener->setUpdatedAt($entity->getUpdatedAt());
        $urlShortener->setCreatedAt($entity->getCreatedAt());
        $urlShortener->setLastUsedAt($entity->getLastUsedAt());
        $urlShortener->setUrl($entity->getUrl());

        return $urlShortener;
    }

    public function removeFromId($id)
    {
        if (is_int($id) && $id>0) {
            $entity = $this->getRepository()->find($id);
            if ($entity instanceof UrlToTag) {
                try {
                  $this->getEm()->remove($entity);
                  $this->getEm()->flush();

                   return true;
                } catch (Exception $e) {
                  $this->doLog(__FUNCTION__."::".$e->getMessage(), 'error');

                  return false;
                }
            } else {
                $this->doLog(__FUNCTION__."::id $id not found", 'error');

                return false;
            }
        }

        return false;
    }
    public function isValidTag($tag)
    {
        $us = $this->loadFromTag($tag);
        if ($us instanceof UrlShortener) {
            $this->doLog(__FUNCTION__."::tag $tag is valid");

            return false;
        } else {
            $this->doLog(__FUNCTION__."::tag $tag is not valid");

            return true;
        }
    }
    /**
     *  generateTag uses TagGenerator and check that the tag produced is not already used
     * @return string if ok else false
     * @
     */
    public function generateTag($size=4, $nbTry=5)
    {
        $this->doLog(__FUNCTION__."::size = $size, nbTry = $nbTry");
        $tagGenerator = new TagGenerator();
        $tagGenerator->setSize($size);
        $cpt = 0;
        $isValid = false;
        $tag = '';
        while ($cpt < $nbTry && $isValid === false) {
            $tag = $tagGenerator->generate();
            if ($this->isValidTag($tag)) {
                $isValid = true;
            }
            $cpt++;
        }
        if ($isValid == true) {
            return $tag;
        }
        $this->doLog(__FUNCTION__."::tag generated is invalid after $nbtry try");

        return false;
    }

    protected function getRepository()
    {
        return $this->em->getRepository('VellozziUrlShortenerBundle:UrlToTag');
    }
    public function getEm()
    {
        return $this->em;
    }

    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }
}