<?php

namespace Vellozzi\UrlShortenerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerTest extends WebTestCase
{
    public function testInvalidRedirect()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/r/aazezeakdnikjnfdjndfsjnvjkffdf65487486564465dfgdfgdfgdfgddf');

        $this->assertTrue($crawler->filter('html:contains("unavailable")')->count() > 0);
    }
}
