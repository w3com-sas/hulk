<?php

namespace W3com\HulkBundle\Service;

use GuzzleHttp\Client;

class ApiManager
{

    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param array $bodyRequest
     * @param $url
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(array $bodyRequest, $url)
    {
        $this->client->request('POST', $url, ['body' => json_encode($bodyRequest)]);
    }
}