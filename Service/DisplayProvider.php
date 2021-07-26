<?php

namespace W3com\HulkBundle\Service;

use Exception;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Filter\FilterSessionManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Query\QueryManager;
use W3com\HulkBundle\Renderer\Renderer;
use W3com\HulkBundle\Util\DataTransformer;
use W3com\HulkBundle\Util\DisplayConstructor;
use W3com\HulkBundle\Util\Indexor;

class DisplayProvider
{
    /**
     * @var FilterSessionManager
     */
    private $filterSessionManager;

    /**
     * @var DisplayConstructor
     */
    private $constructor;

    /**
     * @var SessionManager
     */
    private $session;

    /**
     * @var Indexor
     */
    private $indexor;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var ColumnManager
     */
    private $columnManager;

    /**
     * @var DataTransformer
     */
    private $dataTransformer;

    /**
     * @var QueryManager
     */
    private $queryManager;

    /**
     * @var JsonFinder
     */
    private $jsonFinder;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(
        FilterSessionManager $filterSessionManager,
        DisplayConstructor $constructor,
        SessionManager $sessionManager,
        Indexor $indexor,
        FilterManager $filterManager,
        ColumnManager $columnManager,
        DataTransformer $dataTransformer,
        QueryManager $queryManager,
        JsonFinder $jsonFinder,
        Renderer $renderer,
        CacheManager $cacheManager
    )
    {
        $this->filterSessionManager = $filterSessionManager;
        $this->constructor = $constructor;
        $this->session = $sessionManager;
        $this->indexor = $indexor;
        $this->filterManager = $filterManager;
        $this->columnManager = $columnManager;
        $this->dataTransformer = $dataTransformer;
        $this->queryManager = $queryManager;
        $this->jsonFinder = $jsonFinder;
        $this->renderer = $renderer;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param null $maxResults
     *
     * @throws ReflectionException|InvalidArgumentException
     * @throws Exception
     */
    public function getDisplay($filename, $getRequestParams = [], $maxResults = null): Display
    {
        $display = new Display();
        $display->setFilename($filename);

        if (!$this->cacheManager->isInCache($filename)) {
            $json = $this->jsonFinder->getOnlineJson($filename, $display);
        } else {
            $cacheItem = $this->cacheManager->getCacheItem(CacheManager::DISPLAY_CACHE_KEY);
            $displays = $cacheItem->get();

            if (!array_key_exists($filename, $displays)) {
                $json = $this->jsonFinder->getOnlineJson($filename, $display);
            } else {
                $display->getError()->setFileExist(true);
                $json = $displays[$filename];
            }
        }

        $this->constructor->hydrate($display, $json);
        $this->renderer->buildTemplate($display);

        if ($display->getError()->isFileExist()) {
            $data = $this->queryManager->createDataTableQuery($display, $getRequestParams, $maxResults);

            if ($display->getError()->isClassExist()) {
                $this->dataTransformer->addData($display, $data);
                $this->columnManager->initColumns($display);
                $this->filterManager->initFilters($display);
                $this->filterSessionManager->checkFiltersDefaultValue($display);
                $this->session->setLastRowIndex($display);
                $this->indexor->addIndex($display);
            }
        }

        return $display;
    }

    public function getJsonFinder(): JsonFinder
    {
        return $this->jsonFinder;
    }

    public function getConstructor(): DisplayConstructor
    {
        return $this->constructor;
    }
}
