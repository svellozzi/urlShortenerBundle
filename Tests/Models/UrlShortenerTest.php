<?php

namespace Vellozzi\UrlShortenerBundle\Tests\Models;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use  Vellozzi\UrlShortenerBundle\Models\TagGenerator;
use  Vellozzi\UrlShortenerBundle\Models\UrlShortener;


class UrlShortenerTest extends WebTestCase
{
    
   /**
     * @var EntityManager
     */
    private $_em;
    
    private $testUrl = 'http://www.google.com';

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->_em->beginTransaction();
    }

    /**
     * Rollback changes.
     */
    public function tearDown()
    {
        $this->_em->rollback();
    }
    public function testAddBasic()
    {
      $urlShortener = $this->createObjUrlShortener();
      $ret = $urlShortener->save();
      $this->assertTrue($ret);
    }
    public function testAddBasicError()
    {
      $urlShortener = new UrlShortener($this->_em);
      $ret = $urlShortener->save();
      $this->assertFalse($ret);
    }
    public function testAddMaxAllowedUse()
    {
      $urlShortener = $this->createObjUrlShortener();
      $urlShortener->setMaxAllowedUse(PHP_INT_MAX);
      $ret = $urlShortener->save();
      $this->assertTrue($ret);
    }
    public function testHydrate()
    {
      $urlShortener = $this->createObjUrlShortener();
      //$urlShortener->setExpireAt("2099-01-01 00:00:00");
      $urlShortener->setMaxAllowedUse(PHP_INT_MAX);
      $ret = $urlShortener->save();
      
      $urlShortenerHydrate = new UrlShortener($this->_em);
      $urlShortenerHydrate->setId($urlShortener->getId());
      $urlShortenerHydrate->hydrate();
      print_r($urlShortenerHydrate->getExpireAt());
      $this->assertTrue($urlShortenerHydrate->getUrl() == $this->testUrl);
      $this->assertTrue($urlShortenerHydrate->getMaxAllowedUse() == PHP_INT_MAX);
      $this->assertTrue($urlShortenerHydrate->getShortTag() == $urlShortener->getShortTag());
    }
    
    public function testRemove()
    {
      $urlShortener = $this->createObjUrlShortener();
      if ($urlShortener->save()) {
        $this->assertTrue($urlShortener->remove()); 
      } else {
        $this->assertTrue(1 === 2);
      }
    }
    protected function createObjUrlShortener() 
    {
      $urlShortener = new UrlShortener($this->_em);
      $urlShortener->setUrl($this->testUrl);
      return $urlShortener; 
    }  
 }
