<?php

namespace Ockcyp\Bundle\TodoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/todo');

        $this->assertTrue($crawler->filter('html:contains("Hello!")')->count() > 0);
    }
}
