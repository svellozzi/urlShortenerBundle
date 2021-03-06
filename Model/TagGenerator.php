<?php
namespace Vellozzi\UrlShortenerBundle\Model;
use Vellozzi\UrlShortenerBundle\Exception\InvalidParameterException;
/**
 * TagGenerator generate a tag accordind a fixed  size
 * the tag should be used in a URL
 * @version 0.1
 * @author seb
 */
class TagGenerator extends BaseModel
{
    /**
     * @var string $allowedCharForTag
     */
    private $allowedCharForTag = '';
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
    public function generate()
    {
        $nbAllowedChar = $this->getDictionnarySize();
        $ret = '';
        for ($i=1; $i <= $this->getSize(); $i++) {
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
      if (is_int($size) && $size>0) {
          $this->size = $size;
      } else {
          throw new InvalidParameterException("parameter size must be a positive integer");
      }
    }

    public function getDictionnarySize()
    {
        return strlen($this->allowedCharForTag);
    }
    
    public function getAllowedCharForTag()
    {
        return $this->allowedCharForTag;
    }

    public function setAllowedCharForTag($allowedCharForTag)
    {
        $this->allowedCharForTag = $allowedCharForTag;
    }


}
