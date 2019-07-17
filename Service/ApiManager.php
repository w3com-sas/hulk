<?php

namespace W3com\HulkBundle\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class ApiManager
{

    private $client;

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = new Client();
    }


    public function manageApiCalls(array $data)
    {
        $dataLines = $this->formatData($data);
        $responses = $this->callApi($dataLines, $data);
        return $responses;
    }

    private function formatData(array $data)
    {
        $dataLines = [];

        foreach ($data['data'] as $line) {

            $dataLine = [];

            foreach ($line as $field => $value) {

                if (array_key_exists($field, $data['apiParams']['data'])) {
                    $dataLine['data'][$data['apiParams']['data'][$field]] = $value;
                }

            }

            // TODO : Existe-t-il des actions sans données propre à un objet ?
            if (isset($dataLine['data'])){
                $dataLine['data'] = array_merge($data['apiParams']['data'], $dataLine['data']);
            }

            $dataLines[] = array_merge($data['apiParams'], $dataLine);
        }
        return $dataLines;
    }

    private function callApi(array $dataLines, $data)
    {
        $responses = [];

        foreach ($dataLines as $dataLine) {

            try {
                $response = $this->client->request('POST', $data['urlApi'], $dataLine);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['trace' => $e->getTrace(), 'data' => $dataLine]);
                $response = ['valid' => false, 'data' => $data];
            }

            if (is_array($response)) {
                $responses['errors'][] = $response;
            } elseif (!$response->getBody()['valid']) {
                $responses['errors'][] = $response->getBody();
            } elseif (isset($response) && $response->getBody['valid']) {
                $responses['success'][] = $response->getBody();
            }

        }
        return $responses;
    }
}