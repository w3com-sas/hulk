<?php

namespace W3com\HulkBundle\Query;

use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\Parameters\Parameters;
use W3com\BoomBundle\Service\BoomManager;

class QueryManager
{
    /**
     * @var ModelFinder
     */
    private $modelFinder;

    /**
     * @var BoomManager
     */
    private $boom;


    public function __construct(ModelFinder $finder, BoomManager $boom)
    {
        $this->modelFinder = $finder;
        $this->boom = $boom;
    }

    /**
     * @param DataTable $dataTable
     * @return array
     * @throws \Exception
     */
    public function createDataTableQuery(DataTable $dataTable)
    {
        $this->modelFinder->checkProjectEntities($dataTable);
        $dataTable->getError()->setClassExist(true);

        try {
            $repo = $this->boom->getRepository($dataTable->getEntity());
        } catch (EntityNotFoundException $e) {
            $dataTable->getError()->setClassExist(false);
            return null;
        }

        $params = $repo->createParams();

        // Determine data to get
        $properties = $this->modelFinder->getAvailableProperties($dataTable);

        /** @var Property $property */
        foreach ($properties as $property) {
            $params->addSelect($property->getName());
        }
        $this->addSelectForFilters($dataTable, $params);

        return $repo->findAll($params);
    }

    /**
     * This function allow Filter on hidden column
     *
     * @param DataTable $dataTable
     * @param Parameters $parameters
     * @throws \Exception
     */
    private function addSelectForFilters(DataTable $dataTable, Parameters $parameters)
    {
        /** @var Entity $entity */
        $entity = $this->boom->getGenerator()->getAppInspector()->getProjectEntity($dataTable->getCalcView());

        if (!empty($dataTable->getFilters())){
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {

                if ($entity->getProperty($filter->getFieldName()) === null) {
                    $filter->setActive('N');
                    $filter->addError($filter->getFieldName(). 'n\'éxiste pas.');
                } else {
                    $filter->setActive('Y');
                    $parameters->addSelect($entity->getProperty($filter->getFieldName())->getName());
                }
            }
        }

    }


}