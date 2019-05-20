<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Query\QueryManager;
use W3com\HulkBundle\Util\DataTablesConstructor;
use W3com\HulkBundle\Util\DataTransformer;

class DisplayFilterProvider
{
    private $display;

    private $jsonFinder;

    private $queryManager;

    private $displayConstructor;

    private $filterManager;

    private $dataTransformer;

    private $modelFinder;

    public function __construct(BoomManager $boom, $config)
    {
        $this->display = new DataTable();
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->modelFinder = new ModelFinder($boom);
        $this->queryManager = new QueryManager($this->modelFinder, $boom, $this->display);
        $this->displayConstructor = new DataTablesConstructor();
        $this->filterManager = new FilterManager();
        $this->dataTransformer = new DataTransformer($this->modelFinder);
    }

    /**
     * @param $filename
     * @return DataTable
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getDisplayFilters($filename)
    {
        $json = $this->jsonFinder->getOnlineJson($filename, $this->display);
        $this->displayConstructor->hydrateDataTable($json, $this->display);
        $data = $this->queryManager->createDataTableQuery($this->display);
        $this->dataTransformer->addData($this->display, $data);
        $this->filterManager->initFilters($this->display);
        return $this->display;
    }
}