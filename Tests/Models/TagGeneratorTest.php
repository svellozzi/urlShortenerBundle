<?php

namespace Vellozzi\UrlShortenerBundle\Tests\Models;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use  Vellozzi\UrlShortenerBundle\Models\TagGenerator;

class TagGeneratorTest extends WebTestCase
{
    public function testTagGeneration()
    {
      $tagGenerator = new TagGenerator();
      $tagGenerator->setSize(6);
      $tag = $tagGenerator->generate();
      
      $this->assertTrue(strlen($tag) == $tagGenerator->getSize());
    }
    
    public function testTagGenerationAdvanced()
    {
      $tagGenerator = new TagGenerator();
      $tagGenerator->setSize($tagGenerator->getDictionnarySize() * 4);
      $tag = $tagGenerator->generate();
      $hasUpperCaseLetter = preg_match('/[A-Z]/', $tag);
      $hasLowerCaseLetter = preg_match('/[a-z]/', $tag);
      $hasNumber = preg_match('/[0-9]/', $tag);
      $hasDash = preg_match('/-/', $tag);
      $finalTest = $hasUpperCaseLetter && $hasLowerCaseLetter && $hasNumber && $hasDash;
      $this->assertTrue($finalTest);
    }
}
