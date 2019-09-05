<?php

namespace W3com\HulkBundle\Finder;

use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Util\JsonInspector;

class JsonFinder
{
    private $boom;

    private $config;

    private $baseUri;

    private $jsonUri;

    public function __construct(BoomManager $boom, $config)
    {
        $this->boom = $boom;
        $this->config = $config;
        $this->baseUri = $this->boom->config['odata_service']['base_uri'];
        $this->jsonUri = $this->config['json_display']['url_files'];


    }

    private function createContext()
    {
        $login = $this->boom->config['odata_service']['login']['username']
            . ':' . $this->boom->config['odata_service']['login']['password'];

        $encodedLogin = base64_encode($login);
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => [
                    "Authorization: Basic " . $encodedLogin
                ]
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
            )
        );
        return stream_context_create($opts);
    }

    public function getOnlineJson($filename, Display $dataTable = null)
    {
        $context = $this->createContext();
        $dataTable->getError()->setFileExist(true);

        try {
            $file = file_get_contents($this->baseUri . $this->jsonUri . $filename . '.json', false, $context);
        } catch (\Exception $e){
            $dataTable->getError()->setFileExist(false);
            return null;
        }
        return $file;
    }
}