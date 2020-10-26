<?php

namespace W3com\HulkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DisplayControllerTest extends WebTestCase
{
    public function testShowDisplay()
    {
        $client = static::createClient();
        $client->request('GET', '/display/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowDisplayForm()
    {
        $client = static::createClient();
        $client->request('GET', '/display-form/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
