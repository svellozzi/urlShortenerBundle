<?php
namespace Vellozzi\UrlShortenerBundle\Models;
use Doctrine\ORM\EntityManager;

use Vellozzi\UrlShortenerBundle\Entity\UrlToTag;


class UrlShortener {
    private $em;
    private $urlToTagEntity;
    private $objLog;
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $url
     */
    private $url;

    /**
     * @var string $shortTag
     */
    private $shortTag;

    /**
     * @var \DateTime $createdAt
     */
    private $createdAt;

    /**
     * @var \DateTime $updatedAt
     */
    private $updatedAt;

    /**
     * @var \DateTime $expireAt
     */
    private $expireAt;
    /**
     * @var \DateTime $lastUsedAt
     */
    private $lastUsedAt;

    /**
     * @var integer $maxAllowedUse
     */
    private $maxAllowedUse;

    /**
     * @var integer $nbUsed
     */
    private $nbUsed;

    const INFINITE_NB_ALLOWED_USE = -1;

    public function __construct(EntityManager $em, $log=false)
    {
      $this->setEm($em);
      $this->setObjLog($log);
      $this->init();
    }
    public function init()
    {
      $this->setCreatedAt(new \DateTime());
      $this->setUpdatedAt(new \DateTime());
      $this->setLastUsedAt(NULL);
      $this->setMaxAllowedUse(self::INFINITE_NB_ALLOWED_USE);
      $this->setNbUsed(0);
      $this->setUrlToTagEntity(new UrlToTag());
    }
    protected function _generateTag()
    {
      $tagGenerator = new TagGenerator();
      $tag = $tagGenerator->generate();
      if ($this->_checkTagIsValid($tag)) {
        return $tag;
      } else {
        return false;
      }
    }

    protected function _checkTagIsValid($tag)
    {
      $entity = $this->getEm()->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findOneBy(array('tag' => $tag));
      if ($entity instanceof UrlToTag) {
        return false;
      } else {
        return true;
      }
    }
    public  function generateTag() {
      return $this->_generateTag();
    }
    public function hydrate()
    {
      if (empty($this->id)
          && empty($this->shortTag)) {
        return false;
      }
      if ($this->id > 0) {
        $entity = $this->getEm()->getRepository('VellozziUrlShortenerBundle:UrlToTag')->find($this->getId());
      } else {
        $entity = $this->getEm()->getRepository('VellozziUrlShortenerBundle:UrlToTag')->findOneBy(array('tag' => $this->getShortTag()));
      }
      if ($entity instanceof UrlToTag) {
        $this->setUrlToTagEntity($entity);
        $this->_hydrateObjectFromEntity();
        return true;
      }
      return false;
    }
    
    public function incrNbUsed()
    {
      $nbUsed = (int) $this->getNbUsed();
      $this->setNbUsed(++$nbUsed);
      $this->setLastUsedAt(new \DateTime());
    }
    
    private function _hydrateObjectFromEntity()
    {
      $entity = $this->getUrlToTagEntity();
      if ($entity instanceof UrlToTag) {
        $this->setId($entity->getId());
        $this->setMaxAllowedUse($entity->getMaxAllowedUse());
        $this->setShortTag($entity->getTag());
        $this->setExpireAt($entity->getExpireAt());
        $this->setNbUsed($entity->getNbUsed());
        $this->setUpdatedAt($entity->getUpdatedAt());
        $this->setCreatedAt($entity->getCreatedAt());
        $this->setLastUsedAt($entity->getLastUsedAt());
        $this->setUrl($entity->getUrl());
      }
    }
    private function _hydrateEntityFromObject()
    {
      $entity = $this->getUrlToTagEntity();
      if ($entity instanceof UrlToTag) {
        $entity->setMaxAllowedUse($this->getMaxAllowedUse());
        $entity->setTag($this->getShortTag());
        if (!is_null($this->getExpireAt())) {
          $entity->setExpireAt($this->getExpireAt());
        }
        $entity->setNbUsed($this->getNbUsed());
        $entity->setUpdatedAt($this->getUpdatedAt());
        $entity->setCreatedAt($this->getCreatedAt());
        if (!is_null($this->getLastUsedAt()) && is_object($this->getLastUsedAt())) {
          $entity->setLastUsedAt($this->getLastUsedAt());
        }
        $entity->setUrl($this->getUrl());
        $this->setUrlToTagEntity($entity);
      }
    }

    public function save()
    {
      if (empty($this->url)) {
        return false;
      }
      if (empty($this->id)) {
        if (strlen($this->getShortTag())==0) {
          $tag = false;
          $tag = $this->_generateTag();
          if ($tag === false) {
            return false;
          } else {
            $this->setShortTag($tag);
          }          
        }
      } else {
        $entity = $this->getEm()->getRepository('VellozziUrlShortenerBundle:UrlToTag')->find($this->getId());
      }
      $this->_hydrateEntityFromObject();
      if ($this->getUrlToTagEntity() instanceof UrlToTag) {
        $this->getEm()->persist($this->getUrlToTagEntity());
        $this->getEm()->flush();
      }
      return true;
    }
    
    public function isValid() {
      if ($this->hasExpired() || $this->hasReachedMaxAllowedUse()) {
        return false;
      }
      return true;
    }
    public function hasReachedMaxAllowedUse() {
      if ($this->getMaxAllowedUse() == self::INFINITE_NB_ALLOWED_USE
          || $this->getNbUsed() < $this->getMaxAllowedUse()) {
        return false;
      }
      return true;
    }
    public function hasExpired() 
    {
      $tmp = $this->getExpireAt();
      if (is_null($tmp)) {
        return false;
      } 
      $now = new \DateTime();
      return (bool) ($tmp->getTimestamp() < $now->getTimestamp());
    }
    public function getEm() {
        return $this->em;
    }

    public function setEm($em) {
        $this->em = $em;
    }

    public function getObjLog() {
        return $this->objLog;
    }

    public function setObjLog($objLog) {
        $this->objLog = $objLog;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getShortTag() {
        return $this->shortTag;
    }

    public function setShortTag($shortTag) {
        $this->shortTag = $shortTag;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    public function getExpireAt() {
        return $this->expireAt;
    }

    public function setExpireAt($expireAt) {
        $this->expireAt = $expireAt;
    }

    public function getMaxAllowedUse() {
        return $this->maxAllowedUse;
    }

    public function setMaxAllowedUse($maxAllowedUse) {
        $this->maxAllowedUse = $maxAllowedUse;
    }

    public function getNbUsed() {
        return $this->nbUsed;
    }

    public function setNbUsed($nbUsed) {
        $this->nbUsed = $nbUsed;
    }

    public function getLastUsedAt() {
      return $this->lastUsedAt;
    }

    public function setLastUsedAt($lastUsedAt) {
      $this->lastUsedAt = $lastUsedAt;
    }
    
    public function getUrlToTagEntity() {
      return $this->urlToTagEntity;
    }

    public function setUrlToTagEntity($urlToTagEntity) {
      $this->urlToTagEntity = $urlToTagEntity;
    }
}