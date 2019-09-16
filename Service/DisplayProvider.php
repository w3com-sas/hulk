<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Filter\FilterSessionManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\Display;
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
     * @var Display
     */
    private $display;

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
     * @param BoomGenerator $generator
     * @param UrlGeneratorInterface $router
     * @param FilterSessionManager $filterSessionManager
     */
    public function __construct($config, BoomManager $boom, BoomGenerator $generator, UrlGeneratorInterface $router, FilterSessionManager $filterSessionManager)
    {
        $this->filterSessionManager = $filterSessionManager;
        $this->display = new Display();
        $this->config = $config;
        $this->constructor = new DataTablesConstructor($boom);
        $this->indexor = new Indexor();
        $this->filterManager = new FilterManager();
        $this->columnManager = new ColumnManager();
        $this->modelFinder = new ModelFinder($generator);
        $this->urlManager = new UrlManager($router);
        $this->dataTransformer = new DataTransformer($this->modelFinder, $this->urlManager);
        $this->queryManager = new QueryManager($this->modelFinder, $boom, $generator);
        $this->jsonFinder = new JsonFinder($boom, $config);
    }

    /**
     * @param $filename
     * @param array $getRequestParams
     * @return Display
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getDisplay($filename, $getRequestParams = [])
    {
        $this->display->setFilename($filename);
        $this->constructor->hydrateDataTable($this->jsonFinder->getOnlineJson($filename, $this->display), $this->display);

        if ($this->display->getError()->isFileExist()) {

            $data = $this->queryManager->createDataTableQuery($this->display, $getRequestParams);

            if ($this->display->getError()->isClassExist()) {

                $this->dataTransformer->addData($this->display, $data);

                $this->columnManager->initColumns($this->display);

                $this->filterManager->initFilters($this->display);

                $this->filterSessionManager->checkFiltersDefaultValue($this->display);

                $this->indexor->addIndex($this->display);
            }
        }
        return $this->display;
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