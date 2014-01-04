<?php
namespace Vellozzi\UrlShortenerBundle\Model;

class UrlShortener extends BaseModel
{
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

    public function __construct()
    {
      $this->init();
    }
    public function init()
    {
      $this->setCreatedAt(new \DateTime());
      $this->setUpdatedAt(new \DateTime());
      $this->setLastUsedAt(NULL);
      $this->setMaxAllowedUse(self::INFINITE_NB_ALLOWED_USE);
      $this->setNbUsed(0);
    }

    public function incrNbUsed()
    {
      $nbUsed = (int) $this->getNbUsed();
      $this->setNbUsed(++$nbUsed);
      $this->setLastUsedAt(new \DateTime());
    }

    public function isValid()
    {
      if ($this->hasExpired() || $this->hasReachedMaxAllowedUse()) {
        return false;
      }

      return true;
    }
    public function hasReachedMaxAllowedUse()
    {
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getShortTag()
    {
        return $this->shortTag;
    }

    public function setShortTag($shortTag)
    {
        $this->shortTag = $shortTag;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getExpireAt()
    {
        return $this->expireAt;
    }

    public function setExpireAt($expireAt)
    {
        $this->expireAt = $expireAt;
    }

    public function getMaxAllowedUse()
    {
        return $this->maxAllowedUse;
    }

    public function setMaxAllowedUse($maxAllowedUse)
    {
        $this->maxAllowedUse = $maxAllowedUse;
    }

    public function getNbUsed()
    {
        return $this->nbUsed;
    }

    public function setNbUsed($nbUsed)
    {
        $this->nbUsed = $nbUsed;
    }

    public function getLastUsedAt()
    {
      return $this->lastUsedAt;
    }

    public function setLastUsedAt($lastUsedAt)
    {
      $this->lastUsedAt = $lastUsedAt;
    }
}
