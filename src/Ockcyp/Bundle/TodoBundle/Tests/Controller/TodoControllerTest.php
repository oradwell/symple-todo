<?php

namespace Ockcyp\Bundle\TodoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase
{
    public function testUnauthorized()
    {
        $client = static::createClient();

        $client->request('GET', '/todo');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testList()
    {
        $client = static::createClient();

        $client->request('GET', '/todo', array(), array(), array(
            'PHP_AUTH_USER' => 'omer',
            'PHP_AUTH_PW' => 'omer')
        );

        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));

        json_decode($client->getResponse()->getContent());
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
    }
}
