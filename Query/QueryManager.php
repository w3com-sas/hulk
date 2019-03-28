<?php

namespace W3com\HulkBundle\Query;

use W3com\BoomBundle\Parameters\Clause;
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
    public function createDataTableQuery(DataTable $dataTable, $requestParams = [], $postRequestParams = [])
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
        $this->addSelectForLink($params);
        $this->addGetParamsRequest($requestParams, $params);
        $this->addPostParamsRequest($postRequestParams, $params);
        $this->addPreFilter($dataTable, $params);
        $params->setTop(10000);

        dump($params);
        return $repo->findAll($params);
    }

    /**
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForColumns(Parameters $params)
    {
        if (!empty($this->dataTable->getColumns())) {
            /** @var Column $column */
            foreach ($this->dataTable->getColumns() as $column) {

                if ($column->getType() === Column::TYPE_TEXT || $column->getFieldName() !== null) {

                    if ($this->entity->getProperty($column->getFieldName()) !== null) {
                        $params->addSelect($this->entity->getProperty($column->getFieldName())->getName());
                    } else {
                        $column->setActive('N');
                        $this->dataTable->getError()->addColumnError(
                            sprintf(Error::ERROR_MISSING_FIELD, $column->getFieldName(),
                                $this->dataTable->getCalcView()
                            )
                        );
                    }
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
                        ->addFilterError(
                            sprintf(Error::ERROR_MISSING_FIELD, $filter->getFieldName(), $this->dataTable->getCalcView())
                        );
                } else {
                    $filter->setActive('Y');
                    $params->addSelect($this->entity->getProperty($filter->getFieldName())->getName());
                }
            }
        }

    }

    /**
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForLink(Parameters $params)
    {
        if (!empty($this->dataTable->getColumns())) {

            /** @var Column $column */
            foreach ($this->dataTable->getColumns() as $column) {

                if ($column->getCellAction() !== null) {

                    if ($column->getCellAction()->getFunctionName() == CellAction::FUNCTION_DISPLAY_LINK
                        || $column->getCellAction()->getFunctionName() == CellAction::FUNCTION_LINK) {

                        foreach ($column->getCellAction()->getParams() as $fieldKey => $targetFieldKey) {

                            if ($this->entity->getProperty($fieldKey) !== null) {

                                $params->addSelect($this->entity->getProperty($fieldKey)->getName());

                            } else {

                                $this->dataTable->getError()->addColumnError(
                                    sprintf(Error::ERROR_MISSING_FIELD, $fieldKey, $this->dataTable->getCalcView())
                                );

                            }

                        }
                    }
                }

            }
        }
    }

    /**
     * @param array $getRequestParams
     * @param Parameters $parameters
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    private function addGetParamsRequest($getRequestParams, Parameters $parameters)
    {
        $entity = $this->boom->getGenerator()->getAppInspector()->getProjectEntity(
            $this->dataTable->getCalcView()
        );

        foreach ($getRequestParams as $key => $value) {

            if ($entity->getProperty($key) !== null) {
                try {
                    $parameters->addFilter($entity->getProperty($key)->getName(), $value);
                } catch (\Exception $e) {
                    $this->dataTable->getError()->addRequestParamsError(sprintf(Error::ERROR_MISSING_FIELD,
                        $key, $this->dataTable->getCalcView()));
                }
            }
        }
    }

    private function addPostParamsRequest($postRequestParams, Parameters $parameters)
    {

        $rawFilter = '';

            foreach ($postRequestParams as $field => $value){

                $sapField = substr($field, 3);

                if (end($postRequestParams) === $value) {
                    $filterOperator = '';
                } else {
                    $filterOperator = ' and ';
                }

                if (substr($field, 0, 3) == 'min'){

                    $rawFilter.=sprintf(Clause::GREATER_THAN, $sapField, "'".$value."'".$filterOperator);

                } elseif (substr($field, 0, 3) == 'max'){

                    $rawFilter.=sprintf(Clause::LOWER_THAN, $sapField, "'".$value."'".$filterOperator);

                } elseif ($field !== 'submit' && $field !== '_token') {

                    if ($this->entity->getProperty($field) !== null){
                        $rawFilter.=sprintf(Clause::EQUALS,
                        $field, "'".$value."'".$filterOperator);
                    } else {
                        $this->dataTable->getError()->addRequestParamsError($field. 'does not exist');
                    }
                }
            }
            $parameters->addRawFilter($rawFilter);
    }

    /**
     * @param DataTable $dataTable
     * @param Parameters $parameters
     * @return void
     * @throws \Exception
     */
    private function addPreFilter(DataTable $dataTable, Parameters $parameters)
    {
        if (!empty($dataTable->getFilters())) {
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {

                if ($filter->getType() === Filter::TYPE_PRE_FILTER) {

                    foreach ($filter->getParams() as $field => $value) {
                        $parameters->addFilter($this->entity->getProperty($field)->getName(), $value);
                    }

                }
            }
        }
    }
}