<?php

namespace W3com\HulkBundle\Finder;

use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Model\DataTable;

class JsonFinder
{
    private $boom;

    private $config;

    public function __construct(BoomManager $boom, $config)
    {
        $this->boom = $boom;
        $this->config = $config;

    }

    public function getOnlineJson($filename, DataTable $dataTable)
    {

        $baseUri = $this->boom->config['odata_service']['base_uri'];
        $jsonUri = $this->config['json_display']['url_files'];

        $login =
            $this->boom->config['odata_service']['login']['username']
            . ':' .
            $this->boom->config['odata_service']['login']['password'];

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

        $context = stream_context_create($opts);

        $dataTable->getError()->setFileExist(true);

        try {
            $file = file_get_contents($baseUri . $jsonUri . $filename . '.json', false, $context);
        } catch (\Exception $e){
            $dataTable->getError()->setFileExist(false);
            return null;
        }
        return $file;
    }

}