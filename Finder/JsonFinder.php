<?php

namespace W3com\HulkBundle\Finder;

use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Model\Display;

class JsonFinder
{
    const UNWANTED_FILES = ['.', '..'];

    private $boom;

    private $config;

    private $baseUri;

    private $jsonUri;

    public function __construct(BoomManager $boom, $config)
    {
        $this->boom = $boom;
        $this->config = $config;
        $currentConnection = $boom->getCurrentConnection();
        $this->baseUri = $this->boom->config['odata_service']['connections'][$currentConnection]['uri'];
        $this->jsonUri = $this->config['json_display']['url_files'];


    }

    private function createContext()
    {
        $currentConnection = $this->boom->getCurrentConnection();
        $login = $this->boom->config['odata_service']['connections'][$currentConnection]['username']
            . ':' . $this->boom->config['odata_service']['connections'][$currentConnection]['password'];

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

    public function getOnlineJson($filename, Display $display = null)
    {
        $context = $this->createContext();
        $display = $display === null ? new Display() : $display;
        $display->getError()->setFileExist(true);

        try {
            $file = file_get_contents($this->baseUri . $this->jsonUri . $filename . '.json', false, $context);
        } catch (\Exception $e){
            $display->getError()->setFileExist(false);
            return null;
        }
        return $file;
    }

}