<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Filter\FilterSessionManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Query\QueryManager;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Url\UrlManager;
use W3com\HulkBundle\Util\DataTablesConstructor;
use W3com\HulkBundle\Util\DataTransformer;
use W3com\HulkBundle\Util\Indexor;

class DisplayProvider
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
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var FilterSessionManager
     */
    private $filterSessionManager;

    /**
     * DisplayProvider constructor.
     * @param $config
     * @param BoomManager $boom
     * @param UrlGeneratorInterface $router
     * @param FilterSessionManager $filterSessionManager
     * @throws \ReflectionException
     */
    public function __construct($config, BoomManager $boom, UrlGeneratorInterface $router, FilterSessionManager $filterSessionManager)
    {
        $this->filterSessionManager = $filterSessionManager;
        $this->dataTable = new DataTable();
        $this->config = $config;
        $this->constructor = new DataTablesConstructor($boom);
        $this->indexor = new Indexor();
        $this->filterManager = new FilterManager();
        $this->columnManager = new ColumnManager();
        $this->modelFinder = new ModelFinder($boom);
        $this->urlManager = new UrlManager($router);
        $this->dataTransformer = new DataTransformer($this->modelFinder, $this->urlManager);
        $this->queryManager = new QueryManager($this->modelFinder, $boom, $this->dataTable);
        $this->jsonFinder = new JsonFinder($boom, $config);
    }

    /**
     * @param $filename
     * @param array $getRequestParams
     * @param array $postRequestParams
     * @return DataTable
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getDataTable($filename, $getRequestParams = [])
    {
        $this->dataTable->setFilename($filename);

        $json = $this->jsonFinder->getOnlineJson($filename, $this->dataTable);

        $this->constructor->hydrateDataTable($json, $this->dataTable);

        if ($this->dataTable->getError()->isFileExist()) {

            $data = $this->queryManager->createDataTableQuery($this->dataTable, $getRequestParams);

            if ($this->dataTable->getError()->isClassExist()) {

                $this->dataTransformer->addData($this->dataTable, $data);

                $this->columnManager->initColumns($this->dataTable);

                $this->filterManager->initFilters($this->dataTable);

                $this->filterSessionManager->checkFiltersDefaultValue($this->dataTable);

                $this->indexor->addIndex($this->dataTable);
            }
        }
        return $this->dataTable;
    }

    /**
     * @return JsonFinder
     */
    public function getJsonFinder(): JsonFinder
    {
        return $this->jsonFinder;
    }

    /**
     * @return DataTablesConstructor
     */
    public function getConstructor(): DataTablesConstructor
    {
        return $this->constructor;
    }
}