<?php

namespace W3com\HulkBundle\Service;

use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Query\QueryManager;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Util\DataTablesConstructor;
use W3com\HulkBundle\Util\DataTransformer;
use W3com\HulkBundle\Util\Indexor;

class TableProvider
{

    /**
     * @var QueryManager
     */
    private $queryManager;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var ColumnManager
     */
    private $columnManager;

    /**
     * @var ModelFinder
     */
    private $modelFinder;

    /**
     * @var DataTable
     */
    private $dataTable;

    /**
     * @var array
     */
    private $config;

    /**
     * @var JsonFinder
     */
    private $jsonFinder;

    /**
     * @var Indexor
     */
    private $indexor;

    /**
     * @var DataTransformer
     */
    private $dataTransformer;

    /**
     * @var DataTablesConstructor
     */
    private $constructor;

    /**
     * TableProvider constructor.
     * @param BoomManager $boom
     * @param $config
     */
    public function __construct($config, BoomManager $boom)
    {
        $this->dataTable = new DataTable();
        $this->config = $config;
        $this->constructor = new DataTablesConstructor();
        $this->indexor = new Indexor();
        $this->filterManager = new FilterManager();
        $this->columnManager = new ColumnManager();
        $this->modelFinder = new ModelFinder($boom);
        $this->dataTransformer = new DataTransformer($this->modelFinder);
        $this->queryManager = new QueryManager($this->modelFinder, $boom);
        $this->jsonFinder = new JsonFinder($boom, $config);
    }

    /**
     * @param $filename
     * @return DataTable
     * @throws \Exception
     */
    public function getDataTable($filename)
    {
        $json = $this->jsonFinder->getOnlineJson($filename, $this->dataTable);

        $this->constructor->hydrateDataTable($json, $this->dataTable);


        if ($this->dataTable->getError()->isFileExist()){

            $data = $this->queryManager->createDataTableQuery($this->dataTable);

            if ($this->dataTable->getError()->isClassExist()){

                $this->dataTransformer->addData($this->dataTable, $data);

                $this->columnManager->initColumns($this->dataTable, $data);

                $this->filterManager->initFilters($this->dataTable, $data);

                $this->indexor->addIndex($this->dataTable);

            }

        }
        dump($this->dataTable);
        return $this->dataTable;
    }
}