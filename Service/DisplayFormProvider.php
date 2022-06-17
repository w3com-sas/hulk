<?php

namespace W3com\HulkBundle\Service;

use DateTime;
use Doctrine\Common\Annotations\AnnotationException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Query\QueryManager;
use W3com\HulkBundle\Url\UrlManager;
use W3com\HulkBundle\Util\DataTransformer;
use W3com\HulkBundle\Util\DisplayConstructor;

class DisplayFormProvider
{
    /**
     * @var Display
     */
    private $display;

    /**
     * @var JsonFinder
     */
    private $jsonFinder;

    /**
     * @var QueryManager
     */
    private $queryManager;

    /**
     * @var DisplayConstructor
     */
    private $displayConstructor;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var DataTransformer
     */
    private $dataTransformer;

    /**
     * @var int|mixed
     */
    private $max_result_returned = 500;

    /**
     * @var BoomManager
     */
    private $boom;

    /**
     * @var BoomGenerator
     */
    private $generator;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var DisplayCachingManager
     */
    private $displayCachingManager;

    public function __construct(
        BoomManager $boom,
        BoomGenerator $generator,
        UrlManager $urlManager,
        DisplayConstructor $constructor,
        DisplayCachingManager $displayCachingManager,
        FilterManager $filterManager,
        $config)
    {
        if (array_key_exists('max_result_returned', $config)) {
            $this->max_result_returned = $config['max_result_returned'];
        }
        $this->display = new Display();
        $this->displayCachingManager = $displayCachingManager;
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->queryManager = new QueryManager($boom, $generator,$displayCachingManager);
        $this->displayConstructor = $constructor;
        $this->filterManager = $filterManager;
        $this->dataTransformer = new DataTransformer($urlManager);
        $this->display->isFilter = true;
        $this->boom = $boom;
        $this->generator = $generator;
        $this->cacheManager = new CacheManager();
    }

    /**
     * @param $filename
     * @param array $getParamsRequest
     *
     * @return Display
     * @throws ReflectionException*
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getDisplay($filename, array $getParamsRequest = []): Display
    {
        if (!$this->cacheManager->isInCache($filename)) {
            $json = $this->jsonFinder->getOnlineJson($filename, $this->display);
        } else {
            $cacheItem = $this->cacheManager->getCacheItem(CacheManager::DISPLAY_CACHE_KEY);
            $displays = $cacheItem->get();

            if (!array_key_exists($filename, $displays)) {
                $json = $this->jsonFinder->getOnlineJson($filename, $this->display);
            } else {
                $this->display->getError()->setFileExist(true);
                $json = $displays[$filename];
            }
        }



        $this->display->setFilename($filename);
        $this->displayConstructor->hydrate($this->display, $json);

        // Retreiving all data.
        // Here we analyse if caching is activated
        $data = $this->queryManager->createDataTableQuery($this->display, $getParamsRequest);

        if (!$this->display->getError()->isClassExist()) {
            return $this->display;
        }
        // Hydrate all datas in display (useless in display form mode only for filter hydratation make after).
        $this->dataTransformer->addData($this->display, $data);
        // Construct filter with all datas
        $this->filterManager->initFilters($this->display);

        return $this->display;
    }

    /**
     * @param $calculationView
     * @param array $choices
     * @param array $allFields
     *
     * @throws AnnotationException
     * @throws ReflectionException
     *
     * @return array
     */
    public function getDataFromChoices($calculationView, $choices = [], $allFields = [])
    {
        $this->removeFilenameInChoices($choices);
        $this->removeFilenameInChoices($allFields);
        $appInspector = $this->generator->getAppInspector();
        $entity = $appInspector->getEntity($calculationView);
        $repo = $this->boom->getRepository($entity->getName());
        $params = $repo->createParams();
        foreach ($choices as $field => $value) {
            $value = DataTransformer::reverseDateFormat($value);
            $params->addFilter($entity->getProperty($field)->getName(), $value);
        }

        foreach ($allFields as $field => $value) {
            $params->addSelect($entity->getProperty($field)->getName());
        }

        $results = $repo->findAll($params);
        $formattedData = [];
        /** @var AbstractEntity $hanaEntity */
        foreach ($results as $hanaEntity) {
            $entityArray = json_decode($hanaEntity->getEntityJson(), true);
            foreach ($entityArray as $field => $value) {
                if (!array_key_exists($field, $formattedData)) {
                    $formattedData[$field] = [];
                }
                $formattedData[$field][$value] = $this->dataTransformer->transformDateFormat($value);
            }
        }

        foreach ($formattedData as $field => $values) {
            foreach ($values as $value) {
                $isDate = Datetime::createFromFormat('d/m/Y', $value);
                if (isset($isDate) && $isDate instanceof DateTime) {
                    usort($formattedData[$field], [$this, 'sortDate']);
                } else {
                    ksort($formattedData);
                }
                unset($isDate);
            }
        }

        return $formattedData;
    }

    public function sortDate($x, $y)
    {
        if (null == $x) {
            return -1;
        } elseif (null == $y) {
            return 0;
        }
        if (false === DateTime::createFromFormat('d/m/Y', $x) || false === DateTime::createFromFormat('d/m/Y', $y)) {
            return 0;
        }
        $stampX = DateTime::createFromFormat('d/m/Y', $x)->getTimestamp();
        $stampY = DateTime::createFromFormat('d/m/Y', $y)->getTimestamp();

        if ($stampX > $stampY) {
            return 1;
        } elseif ($stampX < $stampY) {
            return -1;
        }

        return 0;
    }

    public function getQueryManager(): QueryManager
    {
        return $this->queryManager;
    }

    public function getJsonFinder(): JsonFinder
    {
        return $this->jsonFinder;
    }

    public function getMaxResultReturned()
    {
        return $this->max_result_returned;
    }

    private function removeFilenameInChoices(array &$choices)
    {
        if (array_key_exists('filename', $choices)) {
            unset($choices['filename']);
        }
    }
}
