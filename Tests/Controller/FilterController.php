<?php

namespace W3com\HulkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilterController extends WebTestCase
{
    public function testSaveDisplayForm()
    {
        $client = static::createClient();
        $filter = [];
        $filter['field'] = 'value';
        $client->request('POST', '/save-filters', $filter);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}