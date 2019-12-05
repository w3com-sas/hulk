<?php

namespace W3com\HulkBundle\Query;

use Doctrine\Common\Annotations\AnnotationException;
use ReflectionException;
use Symfony\Component\HttpFoundation\ParameterBag;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\Parameters\Clause;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Controller\DisplayFormController;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\CellAction;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Error;
use W3com\HulkBundle\Model\Filter;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Parameters\Parameters;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Url\UrlManager;

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
     * @var BoomGenerator
     */
    private $generator;

    /**
     * QueryManager constructor.
     * @param ModelFinder $finder
     * @param BoomManager $boom
     * @param BoomGenerator $generator
     */
    public function __construct(ModelFinder $finder, BoomManager $boom, BoomGenerator $generator)
    {
        $this->modelFinder = $finder;
        $this->boom = $boom;
        $this->generator = $generator;
    }

    /**
     * @param Display $display
     * @param array $requestParams
     * @param null $top
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function createDataTableQuery(Display $display, $requestParams = [], $top = null)
    {
        $this->appEntity = $this->generator->getAppInspector()
            ->getEntity($display->getCalcView());
        $this->modelFinder->setDataTableEntity($display);
        $display->getError()->setClassExist(true);

        try {
            $repo = $this->boom->getRepository($display->getEntity());
        } catch (EntityNotFoundException $e) {
            $display->getError()->setClassExist(false);
            return null;
        }
        $params = $repo->createParams();

        // Si formulaire alors select pour GROUP BY (Si calcview est en mode aggregate)
        if ($display->isFilter) {
            $this->addSelectForFilters($display, $params);
            $this->addGetParamsRequest($display, $requestParams, $params);
            return $repo->findAll($params);
        }

        $this->addSelectForColumns($display, $params);
        $this->addSelectForFilters($display, $params);
        $this->addSelectForLink($display, $params);
        $this->addGetParamsRequest($display, $requestParams, $params);
        $this->addPreFilter($display, $params);
        $top === null ? $params->setTop($display->getMaxLength()) : $params->setTop($top);
        return $repo->findAll($params);

    }

    /**
     * @param Display $dataTable
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForColumns(Display $dataTable, Parameters $params)
    {
        if (!empty($dataTable->getColumns())) {
            /** @var Column $column */
            foreach ($dataTable->getColumns() as $column) {

                if ($column->getType() === Column::TYPE_TEXT || $column->getFieldName() !== null
                    || $column->getIconFieldName() !== null || $column->getLabelFieldName() !== null) {

                    $atLeastOne = false;

                    if ($this->appEntity->getProperty($column->getFieldName()) !== null) {
                        $params->addSelect($this->appEntity->getProperty($column->getFieldName())->getName());
                        $atLeastOne = true;
                    }

                    if ($this->appEntity->getProperty($column->getIconFieldName()) !== null) {
                        $params->addSelect($this->appEntity->getProperty($column->getIconFieldName())->getName());
                        $atLeastOne = true;
                    }

                    if ($this->appEntity->getProperty($column->getLabelFieldName()) !== null) {
                        $params->addSelect($this->appEntity->getProperty($column->getLabelFieldName())->getName());
                        $atLeastOne = true;
                    }

                    if ($column->getCellAction() != null && $this->appEntity->getProperty($column->getCellAction()->getRenderFieldName()) !== null) {
                        $params->addSelect($this->appEntity->getProperty($column->getCellAction()->getRenderFieldName())->getName());
                        $atLeastOne = true;
                    }

                    if (!$atLeastOne) {
                        $column->setActive('N');
                        $dataTable->getError()->addColumnError(
                            sprintf(Error::ERROR_MISSING_FIELD, $column->getFieldName(),
                                $dataTable->getCalcView()
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
     * @param Display $display
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForFilters(Display $display, Parameters $params)
    {

        if (!empty($display->getFilters())) {

            /** @var Filter $filter */
            foreach ($display->getFilters() as $filter) {

                if ($this->appEntity->getProperty($filter->getFieldName()) === null) {
                    $filter->setActive('N');
                    $display->getError()
                        ->addFilterError(
                            sprintf(Error::ERROR_MISSING_FIELD, $filter->getFieldName(), $display->getCalcView())
                        );
                } else {
                    $filter->setActive('Y');
                    $params->addSelect($this->appEntity->getProperty($filter->getFieldName())->getName());
                }
            }
        }

    }

    /**
     * @param Display $dataTable
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForLink(Display $dataTable, Parameters $params)
    {
        if (!empty($dataTable->getColumns())) {

            /** @var Column $column */
            foreach ($dataTable->getColumns() as $column) {

                if ($column->getCellAction() !== null) {

                    if ($column->getCellAction()->getFunctionName() == CellAction::FUNCTION_DISPLAY_LINK
                        || $column->getCellAction()->getFunctionName() == CellAction::FUNCTION_LINK) {

                        foreach ($column->getCellAction()->getParams() as $fieldKey => $targetFieldKey) {

                            if ($this->appEntity->getProperty($fieldKey) !== null) {

                                $params->addSelect($this->appEntity->getProperty($fieldKey)->getName());

                            } /*else {

                                $dataTable->getError()->addColumnError(
                                    sprintf(Error::ERROR_MISSING_FIELD, $fieldKey, $dataTable->getCalcView())
                                );

                            }*/

                        }
                    }
                }

            }
        }
    }

    /**
     * @param Display $display
     * @param array $getRequestParams
     * @param Parameters $parameters
     * @throws \Exception
     */
    private function addGetParamsRequest(Display $display, $getRequestParams, Parameters $parameters)
    {


        $odsEntity = $this->generator->getOdsInspector()->getEntity($display->getCalcView());

        if ($getRequestParams instanceof ParameterBag) {
            $arrayGetParams = $getRequestParams->all();
        } else {
            $arrayGetParams = $getRequestParams;
        }

        $paramsExist = false;

        foreach ($arrayGetParams as $key => $value) {

            if ($key === 'all'){
                $this->addFilterOnAllProperties($value, $parameters, $display);
            }

            if ($this->appEntity->getProperty($key) !== null) {

                $paramsExist = true;

                $parameters->addFilter($this->appEntity->getProperty($key)->getName(), $value,
                    Clause::EQUALS, Clause:: AND);

                unset($arrayGetParams[$key]);

            }
        }

        foreach ($arrayGetParams as $key => $value) {


            if (substr($key, 0, strlen(UrlManager::INTERVAL_URL_KEY))
                === UrlManager::INTERVAL_URL_KEY) {

                if ($paramsExist && !isset($rawFilter)) {
                    $rawFilter = ' and ';
                } elseif (!$paramsExist && !isset($rawFilter)) {
                    $rawFilter = '';
                }

                if (end($arrayGetParams) === $value) {
                    $filterOperator = '';
                } else {
                    $filterOperator = Clause:: AND;
                }

                $sapField = substr($key, strlen(UrlManager::INTERVAL_URL_KEY));
                $sapQuote = ('Edm.Int32' === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Decimal'
                    === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Double' ===
                    $odsEntity->getProperty($sapField)->getFieldType()) ? "" : "'";
                $sapDatetime = $odsEntity->getProperty($sapField)->getFieldType() === 'Edm.DateTime' ? 'datetime' : null;
                $min = explode('|', $value)[0];
                $max = explode('|', $value)[1];

                if ($min != null && $max != null) {
                    $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime . $sapQuote . $min . $sapQuote . ' and ');
                    $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime . $sapQuote . $max . $sapQuote . $filterOperator);
                } elseif ($min != null) {
                    $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime . $sapQuote . $min . $sapQuote);
                } elseif ($max != null) {
                    $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime . $sapQuote . $max . $sapQuote . $filterOperator);
                }

                if ($min != null || $max != null) {
                    $parameters->addRawFilter($rawFilter);
                }

            }
        }
    }

    /**
     * @param Display $display
     * @param Parameters $parameters
     * @return void
     * @throws \Exception
     */
    private function addPreFilter(Display $display, Parameters $parameters)
    {
        /** @var Filter $filter */
        foreach ($display->getFilters() as $filter) {

            if ($filter->getType() === Filter::TYPE_PRE_FILTER) {

                foreach ($filter->getParams() as $field => $value) {
                    if ($this->appEntity->getProperty($field) !== null) {
                        $parameters->addFilter($this->appEntity->getProperty($field)->getName(), $value);
                    } else {
                        $display->getError()
                            ->addFilterError(
                                sprintf(Error::ERROR_MISSING_FIELD, $filter->getFieldName(), $display->getCalcView())
                            );
                    }
                }
            }
        }
    }

    private function addFilterOnAllProperties($value, Parameters $parameters, Display $display)
    {
        /** @var Property $property */
        foreach ($this->appEntity->getProperties() as $property){
            if (in_array($property->getField(), $display->getColumnsFieldNames()) && ($property->getFieldType() === 'string' || $property->getFieldType() === 'int')){
                $transformFunction = $property->getFieldType() === 'string'? Clause::TO_LOWER : null;
                $parameters->addFilter($property->getName(), $value, Clause::EQUALS, Clause::OR, $transformFunction);
            }
        }
    }

}