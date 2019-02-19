<?php

namespace W3com\HulkBundle\Service;

use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Util\JsonInspector;

class JsonFileManager
{
    private $jsonInspector;

    private $modelFinder;

    private $jsonFinder;

    private $tableProvider;

    public function __construct($config, BoomManager $boom)
    {
        $this->jsonInspector = new JsonInspector();
        $this->modelFinder = new ModelFinder($boom);
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->tableProvider = new TableProvider($config, $boom);
    }

    public function inspectJsonFiles()
    {
        $this->jsonFinder->getAllFiles($this->jsonInspector);
        return $this->jsonInspector;
    }
}