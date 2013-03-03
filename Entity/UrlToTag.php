<?php

namespace Vellozzi\UrlShortenerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UrlToTag
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Vellozzi\UrlShortenerBundle\Entity\UrlToTagRepository")
 */
class UrlToTag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=8)
     */
    private $tag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUsedAt", type="datetime", nullable=true)
     */
    private $lastUsedAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expireAt", type="datetime", nullable=true)
     */
    private $expireAt;
    
    /**
     * @var \integer
     *
     * @ORM\Column(name="nbUsed", type="integer")
     */
    private $nbUsed;
    /**
     * @var \integer
     *
     * @ORM\Column(name="maxAllowedUse", type="integer")
     */
    private $maxAllowedUse;

    public function __construct() {
      $this->nbUsed = 0;
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return UrlToTag
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return UrlToTag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UrlToTag
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UrlToTag
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set lastUsedAt
     *
     * @param \DateTime $lastUsedAt
     * @return UrlToTag
     */
    public function setLastUsedAt($lastUsedAt)
    {
        $this->lastUsedAt = $lastUsedAt;
    
        return $this;
    }

    /**
     * Get lastUsedAt
     *
     * @return \DateTime 
     */
    public function getLastUsedAt()
    {
        return $this->lastUsedAt;
    }

    /**
     * Set nbUsed
     *
     * @param integer $nbUsed
     * @return UrlToTag
     */
    public function setNbUsed($nbUsed)
    {
        $this->nbUsed = $nbUsed;
    
        return $this;
    }

    /**
     * Get nbUsed
     *
     * @return integer 
     */
    public function getNbUsed()
    {
        return $this->nbUsed;
    }

    /**
     * Set maxAllowedUse
     *
     * @param integer $maxAllowedUse
     * @return UrlToTag
     */
    public function setMaxAllowedUse($maxAllowedUse)
    {
        $this->maxAllowedUse = $maxAllowedUse;
    
        return $this;
    }

    /**
     * Get maxAllowedUse
     *
     * @return integer 
     */
    public function getMaxAllowedUse()
    {
        return $this->maxAllowedUse;
    }

    /**
     * Set expireAt
     *
     * @param \DateTime $expireAt
     * @return UrlToTag
     */
    public function setExpireAt($expireAt)
    {
        $this->expireAt = $expireAt;
    
        return $this;
    }

    /**
     * Get expireAt
     *
     * @return \DateTime 
     */
    public function getExpireAt()
    {
        return $this->expireAt;
    }
}