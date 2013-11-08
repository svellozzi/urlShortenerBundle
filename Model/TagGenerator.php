<?php
namespace Vellozzi\UrlShortenerBundle\Model;
/**
 * TagGenerator generate a tag accordind a fixed  size
 * the tag should be used in a URL
 * @version 0.1
 * @author seb
 */
class TagGenerator extends BaseModel {
    /**
     * @var string $allowedCharForTag
     */
    private $allowedCharForTag = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
    /**
     * @var int $size
     */
    private $size = 4;
    /*
     * default constructor
     */
    public function __construct() {}
    /*
     * generate a random tag according to size setted (default: 4)
     */
    function generate()
    {
      $nbAllowedChar = $this->getDictionnarySize();
      $ret = '';
      for ($i=1; $i <= $this->getSize(); $i++)
      {
        $index = mt_rand(0,($nbAllowedChar-1));
        $ret .= $this->allowedCharForTag[$index];
      }
      return $ret;
    }
    
    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
      if (is_numeric($size))
      {
        $this->size = $size;
      }
    }
    
    public function getDictionnarySize()
    {
        return strlen($this->allowedCharForTag);
    }
}