<?php

namespace W3com\HulkBundle\Query;

use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\CellAction;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Error;
use W3com\HulkBundle\Model\Filter;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\Model\Entity;
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

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var DataTable
     */
    private $dataTable;


    /**
     * QueryManager constructor.
     * @param ModelFinder $finder
     * @param BoomManager $boom
     * @param DataTable $dataTable
     * @throws \ReflectionException
     */
    public function __construct(ModelFinder $finder, BoomManager $boom, DataTable $dataTable)
    {
        $this->modelFinder = $finder;
        $this->boom = $boom;
        $this->dataTable = $dataTable;
    }

    /**
     * @param DataTable $dataTable
     * @param $requestParams
     * @return array
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function createDataTableQuery(DataTable $dataTable, $requestParams)
    {
        $this->entity = $this->boom->getGenerator()->getAppInspector()
            ->getProjectEntity($dataTable->getCalcView());

        $this->modelFinder->checkProjectEntities($dataTable);
        $dataTable->getError()->setClassExist(true);

        try {
            $repo = $this->boom->getRepository($dataTable->getEntity());
        } catch (EntityNotFoundException $e) {
            $dataTable->getError()->setClassExist(false);
            return null;
        }

        $params = $repo->createParams();

        $this->addSelectForColumns($params);
        $this->addSelectForFilters($params);
        $this->addSelectForDisplayLink($params);
        $this->addParamsRequest($requestParams, $params);
        $params->setTop(10000);

        return $repo->findAll($params);
    }

    /**
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForColumns(Parameters $params)
    {
        /** @var Column $column */
        foreach ($this->dataTable->getColumns() as $column){

            if ($column->getType() === Column::TYPE_TEXT||$column->getFieldName() !== null){

                if ($this->entity->getProperty($column->getFieldName()) !== null){
                    $params->addSelect($this->entity->getProperty($column->getFieldName())->getName());
                } else {
                    $column->setActive('N');
                }
            }

        }
    }

    /**
     * This function allow Filter on hidden column
     *
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForFilters(Parameters $params)
    {

        if (!empty($this->dataTable->getFilters())) {
            /** @var Filter $filter */
            foreach ($this->dataTable->getFilters() as $filter) {

                if ($this->entity->getProperty($filter->getFieldName()) === null) {
                    $filter->setActive('N');
                    $this->dataTable->getError()
                        ->addFilterError($filter->getFieldName() . 'n\'existe pas');
                } else {
                    $filter->setActive('Y');
                    $params->addSelect($this->entity->getProperty($filter->getFieldName())->getName());
                }
            }
        }

    }

    /**
     * @param Parameters $params
     */
    private function addSelectForDisplayLink(Parameters $params)
    {
        /** @var Column $column */
        foreach ($this->dataTable->getColumns() as $column) {
            if ($column->getCellAction() !== null) {
                if ($column->getCellAction()->getFunctionName() == CellAction::FUNCTION_DISPLAY_LINK) {
                    foreach ($column->getCellAction()->getParams() as $fieldKey => $targetFieldKey) {

                         try {
                            $params->addSelect($this->entity->getProperty($fieldKey)->getName());
                        } catch (\Exception $e){
                            $this->dataTable->getError()->addColumnError(
                                sprintf(Error::ERROR_MISSING_FIELD, $fieldKey, $column)
                            );
                        }
                    }
                }
            }

        }
    }

    /**
     * @param array $requestParams
     * @param Parameters $parameters
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    private function addParamsRequest($requestParams, Parameters $parameters)
    {
        $entity = $this->boom->getGenerator()->getAppInspector()->getProjectEntity(
            $this->dataTable->getCalcView()
        );

        foreach ($requestParams as $key => $value){

            if ($entity->getProperty($key) !== null){
                try {
                    $parameters->addFilter($entity->getProperty($key)->getName(), $value);
                } catch (\Exception $e){
                    $this->dataTable->getError()->addRequestParamsError(sprintf(Error::ERROR_MISSING_FIELD,
                        $key, $this->dataTable->getCalcView()));
                }
            }
        }
    }


}