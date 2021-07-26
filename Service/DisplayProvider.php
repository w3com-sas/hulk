<?php

namespace W3com\HulkBundle\Service;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Filter\FilterSessionManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Query\QueryManager;
use W3com\HulkBundle\Renderer\Renderer;
use W3com\HulkBundle\Url\UrlManager;
use W3com\HulkBundle\Util\DataTransformer;
use W3com\HulkBundle\Util\DisplayConstructor;
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
     * @var DisplayConstructor
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
     * @var SessionManager
     */
    private $session;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(
        array $config,
        BoomManager $boom,
        BoomGenerator $generator,
        UrlGeneratorInterface $router,
        FilterSessionManager $filterSessionManager,
        LoggerInterface $logger,
        DisplayConstructor $constructor,
        SessionManager $sessionManager
    )
    {
        $this->filterSessionManager = $filterSessionManager;
        $this->session = $sessionManager;
        $this->constructor = $constructor;
        $this->indexor = new Indexor();
        $this->filterManager = new FilterManager();
        $this->columnManager = new ColumnManager();
        $this->urlManager = new UrlManager($router, $logger);
        $this->dataTransformer = new DataTransformer($this->urlManager);
        $this->queryManager = new QueryManager($boom, $generator);
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->renderer = new Renderer();
        $this->cacheManager = new CacheManager();
    }

    /**
     * @param null $maxResults
     *
     * @throws ReflectionException|InvalidArgumentException
     * @throws Exception
     */
    public function getDisplay($filename, array $getRequestParams = [], $maxResults = null): Display
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
