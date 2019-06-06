<?php

namespace W3com\HulkBundle\Query;

use Doctrine\Common\Annotations\AnnotationException;
use ReflectionException;
use Symfony\Component\HttpFoundation\ParameterBag;
use W3com\BoomBundle\Parameters\Clause;
use W3com\HulkBundle\Controller\DisplayFiltersController;
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
    private $appEntity;


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
     * @param array $requestParams
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function createDataTableQuery(DataTable $dataTable, $requestParams = [])
    {
        $this->appEntity = $this->boom->getGenerator()->getAppInspector()
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
        $this->addPreFilter($dataTable, $params);
        $params->setTop(10000);

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

                    if ($this->appEntity->getProperty($column->getFieldName()) !== null) {
                        $params->addSelect($this->appEntity->getProperty($column->getFieldName())->getName());
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

                if ($this->appEntity->getProperty($filter->getFieldName()) === null) {
                    $filter->setActive('N');
                    $this->dataTable->getError()
                        ->addFilterError(
                            sprintf(Error::ERROR_MISSING_FIELD, $filter->getFieldName(), $this->dataTable->getCalcView())
                        );
                } else {
                    $filter->setActive('Y');
                    $params->addSelect($this->appEntity->getProperty($filter->getFieldName())->getName());
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

                            if ($this->appEntity->getProperty($fieldKey) !== null) {

                                $params->addSelect($this->appEntity->getProperty($fieldKey)->getName());

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
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function addGetParamsRequest($getRequestParams, Parameters $parameters)
    {

        $odsEntity = $this->boom->getGenerator()->getOdsInspector()->getOdsEntity(
            $this->dataTable->getCalcView()
        );

        if ($getRequestParams instanceof ParameterBag){
            $arrayGetParams = $getRequestParams->all();
        } else {
            $arrayGetParams = $getRequestParams;
        }

        $paramsExist = false;

        foreach ($arrayGetParams as $key => $value) {

            if ($this->appEntity->getProperty($key) !== null) {

                $paramsExist = true;

                $parameters->addFilter($this->appEntity->getProperty($key)->getName(), $value,
                    Clause::EQUALS, Clause::AND);

                unset($arrayGetParams[$key]);

            }
        }

        foreach ($arrayGetParams as $key => $value) {

            if (substr($key, 0, strlen(DisplayFiltersController::INTERVAL_URL_KEY))
                === DisplayFiltersController::INTERVAL_URL_KEY) {

                if ($paramsExist){
                    $rawFilter = ' and ';
                } else {
                    $rawFilter = '';
                }

                if (end($arrayGetParams) === $value) {
                    $filterOperator = '';
                } else {
                    $filterOperator = Clause:: AND;
                }

                $sapField = substr($key, strlen(DisplayFiltersController::INTERVAL_URL_KEY));
                $sapQuote = ('Edm.Int32' === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Decimal'
                    === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Double' ===
                    $odsEntity->getProperty($sapField)->getFieldType()) ? "" : "'";

                $min = explode('|', $value)[0];
                $max = explode('|', $value)[1];
                $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapQuote . $min . $sapQuote . ' and ');
                $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapQuote . $max . $sapQuote . $filterOperator);
                $parameters->addRawFilter($rawFilter);

            }
        }
    }

    private function addPostParamsRequest($postRequestParams, Parameters $parameters)
    {

        $odsEntity = $this->boom->getGenerator()->getOdsInspector()->getOdsEntity(
            $this->dataTable->getCalcView()
        );
        $rawFilter = '';

        $formattedPostRequestParams = array_filter($postRequestParams, function ($value) {
            return ($value != "");
        });

        foreach ($formattedPostRequestParams as $field => $value) {

            $sapField = substr($field, 3);


            if (end($formattedPostRequestParams) === $value) {
                $filterOperator = '';
            } else {
                $filterOperator = ' and ';
            }

            if (substr($field, 0, 3) == 'min') {

                $sapQuote = ('Edm.Int32' === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Decimal'
                    === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Double' ===
                    $odsEntity->getProperty($sapField)->getFieldType()) ? "" : "'";


                $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapQuote . $value . $sapQuote . $filterOperator);

            } elseif (substr($field, 0, 3) == 'max') {
                $sapQuote = ('Edm.Int32' === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Decimal'
                    === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Double' ===
                    $odsEntity->getProperty($sapField)->getFieldType()) ? "" : "'";


                $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapQuote . $value . $sapQuote . $filterOperator);

            } elseif ($field !== 'submit' && $field !== '_token') {

                $quote = ('Edm.Int32' === $odsEntity->getProperty($field)->getFieldType() || 'Edm.Decimal'
                    === $odsEntity->getProperty($field)->getFieldType() || 'Edm.Double' ===
                    $odsEntity->getProperty($field)->getFieldType()) ? "" : "'";

                if ($this->appEntity->getProperty($field) !== null) {

                    $rawFilter .= sprintf(Clause::EQUALS,
                        $field, $quote . $value . $quote . $filterOperator);
                } else {
                    $this->dataTable->getError()->addRequestParamsError($field . 'does not exist');
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
                        $parameters->addFilter($this->appEntity->getProperty($field)->getName(), $value);
                    }
                }
            }
        }
    }
}