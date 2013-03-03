<?php

namespace Vellozzi\UrlShortenerBundle\Tests\Models;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use  Vellozzi\UrlShortenerBundle\Models\TagGenerator;
use  Vellozzi\UrlShortenerBundle\Models\UrlShortener;


class UrlShortenerTest extends WebTestCase
{
    public function testAdd()
    {
      print_r(get_included_files());
       echo get_class(static::$kernel->getContainer()).'****';die('seb');
     /*
      $urlShortener = new UrlShortener($this->getDoctrine()->getEntityManager());
      $urlShortener->setUrl('htttp://www.google.com');
      $urlShortener->save();
      $this->assertTrue(strlen($tag) == $tagGenerator->getSize());
      * 
      */
    }
    
    /*
     * 
    // extends \PHPUnit_Framework_TestCase
    protected function getUser()
    {
        return $this->getMockForAbstractClass('FOS\UserBundle\Model\User');
    }
     */
    
 }
