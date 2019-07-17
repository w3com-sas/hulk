<?php

namespace W3com\HulkBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class ApiManager
{

    private $client;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = new Client();
    }

    /**
     * @param array $bodyRequest
     * @param $url
     * @return bool
     * @throws GuzzleException
     */
    public function post(array $bodyRequest, $url)
    {
       $response = $this->client->request('POST', $url, ['body' => json_encode($bodyRequest)]);
       $this->logger->info($response->getBody());

    }
}