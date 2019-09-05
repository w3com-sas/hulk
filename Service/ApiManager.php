<?php

namespace W3com\HulkBundle\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
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
        $toRemoveFields = [];

        foreach ($data['data'] as $line) {

            $dataLine = [];

            foreach ($line as $field => $value) {

                foreach ($data['apiParams']['data'] as $targetField => $targetKey){

                    if ($targetKey === $field){

                        $dataLine['data'][$targetField] = $value;
                        $toRemoveFields[] = $field;
                    }
                }
            }

            // TODO : Existe-t-il des actions sans données propre à un objet ?
            if (isset($dataLine['data'])){
                $dataLine['data'] = array_merge($data['apiParams']['data'], $dataLine['data']);

                foreach ($toRemoveFields as $toRemoveField){
                    unset($dataLine['data'][$toRemoveField]);
                }

            }

            $dataLines[] = array_merge($data['apiParams'], $dataLine);
        }
        return $dataLines;
    }

    private function callApi(array $dataLines, $data)
    {
        $responses = [];
        $responses['errors'] = [];
        $responses['success'] = [];

        foreach ($dataLines as $dataLine) {

            try {
                $response = $this->convertContentToArray(
                    $this->client->request('POST', $data['urlApi'], ['body' => json_encode($dataLine)])
                );
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['trace' => $e->getTrace(), 'data' => $dataLine]);
                $response = ['valid' => false, 'data' => $data];
            }


            if ($response['valid']) {
                $responses['success'][] = 'Réussie pour : '.implode(',', $dataLine['apiParams']['data']);
            } else {
                $responses['errors'][] = $response['error'];
            }

        }
        return $responses;
    }

    private function convertContentToArray(ResponseInterface $response)
    {
        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();
        // Remove HTML and other useless things
        $json = substr($body, strpos($body, '{'), strpos($body, '}') + 1);
        return json_decode($json, true);
    }
}