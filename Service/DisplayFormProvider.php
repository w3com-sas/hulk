<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Query\QueryManager;
use W3com\HulkBundle\Util\DataTablesConstructor;
use W3com\HulkBundle\Util\DataTransformer;

class DisplayFormProvider
{
    private $display;

    private $jsonFinder;

    private $queryManager;

    private $displayConstructor;

    private $filterManager;

    private $dataTransformer;

    private $modelFinder;

    public function __construct(BoomManager $boom, BoomGenerator $generator, $config)
    {
        $this->display = new Display();
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->modelFinder = new ModelFinder($generator);
        $this->queryManager = new QueryManager($this->modelFinder, $boom, $generator);
        $this->displayConstructor = new DataTablesConstructor($boom);
        $this->filterManager = new FilterManager();
        $this->dataTransformer = new DataTransformer($this->modelFinder);
        $this->display->isFilter = true;
    }

    /**
     * @param $filename
     * @return Display
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getDisplay($filename)
    {
        $json = $this->jsonFinder->getOnlineJson($filename, $this->display);
        $this->displayConstructor->hydrateDataTable($json, $this->display);
        $data = $this->queryManager->createDataTableQuery($this->display);

        if (!$this->display->getError()->isClassExist()){
            return $this->display;
        }

        $this->dataTransformer->addData($this->display, $data);
        $this->filterManager->initFilters($this->display);
        return $this->display;
    }
}