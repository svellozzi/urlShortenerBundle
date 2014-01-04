<?php

namespace Vellozzi\UrlShortenerBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use  Vellozzi\UrlShortenerBundle\Model\TagGenerator;
use  Vellozzi\UrlShortenerBundle\Model\UrlShortener;


class UrlShortenerTest extends WebTestCase
{
    
   /**
     * @var EntityManager
     */
    private $_em;
    private $urlToTagManager;
    
    private $testUrl = 'http://www.google.com';

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->urlToTagManager =  $kernel->getContainer()->get('vellozzi_urlshortener.urlshortener_manager');
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
        $res =  $this->urlToTagManager->save($urlShortener);
        $this->assertTrue($ret);
    }
    public function testAddBasicError()
    {
        $urlShortener = new UrlShortener();
        $ret = $this->urlToTagManager->save($urlShortener);
        $this->assertFalse($ret);
    }
    public function testAddMaxAllowedUse()
    {
        $urlShortener = $this->createObjUrlShortener();
        $urlShortener->setMaxAllowedUse(PHP_INT_MAX);
        $ret = $this->urlToTagManager->save($urlShortener);
        $this->assertTrue($ret);
    }
    public function testHydrate()
    {
        $urlShortener = $this->createObjUrlShortener();
        $urlShortener->setMaxAllowedUse(PHP_INT_MAX);
        $ret = $this->urlToTagManager->save($urlShortener);

        $urlShortenerHydrate = $this->UrlToTagManager->loadFromId($urlShortener->getId());
        $this->assertTrue($urlShortenerHydrate->getUrl() == $this->testUrl);
        $this->assertTrue($urlShortenerHydrate->getMaxAllowedUse() == PHP_INT_MAX);
        $this->assertTrue($urlShortenerHydrate->getShortTag() == $urlShortener->getShortTag());
    }
    
//    public function testRemove()
//    {
//      $urlShortener = $this->createObjUrlShortener();
//      $ret = $this->urlToTagManager->save($urlShortener);
//      if ($ret) {
//        $this->assertTrue($urlShortener->remove()); 
//      } else {
//        $this->assertTrue(1 === 2);
//      }
//    }
    protected function createObjUrlShortener() 
    {
        $urlShortener = new UrlShortener();
        $urlShortener->setUrl($this->testUrl);
        $tag = $this->urlToTagManager->generateTag();
        $urlShortener->setShortTag($tag);
        return $urlShortener; 
    }  
 }
