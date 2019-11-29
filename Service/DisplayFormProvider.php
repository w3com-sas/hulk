<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Query\QueryManager;
use W3com\HulkBundle\Util\DisplayConstructor;
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

    /**
     * @var BoomManager
     */
    private $boom;

    /**
     * @var BoomGenerator
     */
    private $generator;

    public function __construct(BoomManager $boom, BoomGenerator $generator, $config)
    {
        $this->display = new Display();
        $this->jsonFinder = new JsonFinder($boom, $config);
        $this->modelFinder = new ModelFinder($generator);
        $this->queryManager = new QueryManager($this->modelFinder, $boom, $generator);
        $this->displayConstructor = new DisplayConstructor($boom);
        $this->filterManager = new FilterManager();
        $this->dataTransformer = new DataTransformer($this->modelFinder);
        $this->display->isFilter = true;
        $this->boom = $boom;
        $this->generator = $generator;
    }

    /**
     * @param $filename
     * @param array $getParamsRequest
     * @return Display
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getDisplay($filename, $getParamsRequest = [])
    {
        $json = $this->jsonFinder->getOnlineJson($filename, $this->display);
        $this->display->setFilename($filename);
        $this->displayConstructor->hydrateDataTable($json, $this->display);
        $data = $this->queryManager->createDataTableQuery($this->display, $getParamsRequest);

        if (!$this->display->getError()->isClassExist()) {
            return $this->display;
        }

        $this->dataTransformer->addData($this->display, $data);
        $this->filterManager->initFilters($this->display);
        return $this->display;
    }

    /**
     * @param $calculationView
     * @param $choices
     * @return array
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getDataFromChoices($calculationView, $choices = [])
    {
        $appInspector = $this->generator->getAppInspector();
        $entity = $appInspector->getEntity($calculationView);
        $repo = $this->boom->getRepository($entity->getName());
        $params = $repo->createParams();
        foreach ($choices as $field => $value) {
            $params->addFilter($entity->getProperty($field)->getName(), $value);
        }
        $results = $repo->findAll($params);

        $arrayResults = [];
        /** @var AbstractEntity $hanaEntity */
        foreach ($results as $hanaEntity) {
            $entityArray = json_decode($hanaEntity->getEntityJson(), true);
            $formattedData = [];
            foreach ($entityArray as $field => $value){
                $formattedData[$field] = $this->dataTransformer->transformDateFormat($value);
            }
            $arrayResults[] = $formattedData;
        }
        return $arrayResults;
    }


}