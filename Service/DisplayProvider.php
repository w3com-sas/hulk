<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Psr\Log\LoggerInterface;
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
use W3com\HulkBundle\Renderer\Renderer;
use W3com\HulkBundle\Url\UrlManager;
use W3com\HulkBundle\Util\DisplayConstructor;
use W3com\HulkBundle\Util\DataTransformer;
use W3com\HulkBundle\Util\Indexor;

class DisplayProvider
{

    /** @var QueryManager */
    private $queryManager;

    /** @var FilterManager */
    private $filterManager;

    /** @var ColumnManager */
    private $columnManager;

    /** @var array */
    private $config;

    /** @var JsonFinder */
    private $jsonFinder;

    /** @var Indexor */
    private $indexor;

    /** @var DataTransformer */
    private $dataTransformer;

    /** @var DisplayConstructor */
    private $constructor;

    /** @var UrlManager */
    private $urlManager;

    /** @var FilterSessionManager */
    private $filterSessionManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var Renderer */
    private $renderer;

    /**
     * DisplayProvider constructor.
     * @param $config
     * @param BoomManager $boom
     * @param BoomGenerator $generator
     * @param UrlGeneratorInterface $router
     * @param FilterSessionManager $filterSessionManager
     * @param LoggerInterface $logger
     * @param DisplayConstructor $constructor
     * @param Renderer $renderer
     */
    public function __construct($config, BoomManager $boom, BoomGenerator $generator, UrlGeneratorInterface $router, FilterSessionManager $filterSessionManager,
                                LoggerInterface $logger, DisplayConstructor $constructor)
    {
        $this->filterSessionManager = $filterSessionManager;
        $this->logger = $logger;
        $this->config = $config;
        $this->constructor = $constructor;
        $this->indexor = new Indexor();
        $this->filterManager = new FilterManager();
        $this->columnManager = new ColumnManager();
        $this->urlManager = new UrlManager($router, $logger);
        $this->dataTransformer = new DataTransformer($this->urlManager);
        $this->queryManager = new QueryManager($boom, $generator);
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->renderer = new Renderer();
    }

    /**
     * @param $filename
     * @param array $getRequestParams
     * @param null $maxResults
     * @return Display
     * @throws \ReflectionException
     */
    public function getDisplay($filename, $getRequestParams = [], $maxResults = null)
    {
        $display = new Display();
        $display->setFilename($filename);
        $this->constructor->hydrate($display, $this->jsonFinder->getOnlineJson($filename, $display));
        $this->renderer->buildTemplate($display);

        if ($display->getError()->isFileExist()) {

            $data = $this->queryManager->createDataTableQuery($display, $getRequestParams, $maxResults);

            if ($display->getError()->isClassExist()) {

                $this->dataTransformer->addData($display, $data);

                $this->columnManager->initColumns($display);

                $this->filterManager->initFilters($display);

                $this->filterSessionManager->checkFiltersDefaultValue($display);

                $this->indexor->addIndex($display);
            }
        }
        return $display;
    }


    /**
     * @return JsonFinder
     */
    public function getJsonFinder(): JsonFinder
    {
        return $this->jsonFinder;
    }

    /**
     * @return DisplayConstructor
     */
    public function getConstructor(): DisplayConstructor
    {
        return $this->constructor;
    }
}