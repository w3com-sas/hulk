<?php

namespace W3com\HulkBundle\Query;

use Doctrine\Common\Annotations\AnnotationException;
use ReflectionException;
use Symfony\Component\HttpFoundation\ParameterBag;
use W3com\BoomBundle\Parameters\Clause;
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
     * QueryManager constructor.
     * @param ModelFinder $finder
     * @param BoomManager $boom
     * @param Display $dataTable
     */
    public function __construct(ModelFinder $finder, BoomManager $boom, Display $dataTable)
    {
        $this->modelFinder = $finder;
        $this->boom = $boom;
    }

    /**
     * @param Display $dataTable
     * @param array $requestParams
     * @param bool $dataFilter
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function createDataTableQuery(Display $dataTable, $requestParams = [])
    {

        $this->appEntity = $this->boom->getGenerator()->getAppInspector()
            ->getProjectEntity($dataTable->getCalcView());

        $this->modelFinder->setDataTableEntity($dataTable);
        $dataTable->getError()->setClassExist(true);

        try {
            $repo = $this->boom->getRepository($dataTable->getEntity());
        } catch (EntityNotFoundException $e) {
            $dataTable->getError()->setClassExist(false);
            return null;
        }

        $params = $repo->createParams();

        // Si formulaire alors select pour GROUP BY (Si calcview est en mode aggregate)
        if ($dataTable->isFilter) {
            $this->addSelectForFilters($dataTable, $params);
            return $repo->findAll($params);
        }

        $this->addSelectForColumns($dataTable, $params);
        $this->addSelectForFilters($dataTable, $params);
        $this->addSelectForLink($dataTable, $params);
        $this->addGetParamsRequest($dataTable, $requestParams, $params);
        $this->addPreFilter($dataTable, $params);
        $params->setTop($dataTable->getMaxLength());

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

                if ($column->getType() === Column::TYPE_TEXT || $column->getFieldName() !== null) {

                    if ($this->appEntity->getProperty($column->getFieldName()) !== null) {
                        $params->addSelect($this->appEntity->getProperty($column->getFieldName())->getName());
                    } else {
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
     * @param Display $dataTable
     * @param array $getRequestParams
     * @param Parameters $parameters
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function addGetParamsRequest(Display $dataTable, $getRequestParams, Parameters $parameters)
    {

        $odsEntity = $this->boom->getGenerator()->getOdsInspector()->getOdsEntity(
            $dataTable->getCalcView()
        );

        if ($getRequestParams instanceof ParameterBag) {
            $arrayGetParams = $getRequestParams->all();
        } else {
            $arrayGetParams = $getRequestParams;
        }

        $paramsExist = false;

        foreach ($arrayGetParams as $key => $value) {

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

                dump($sapField);
                /*
               if ($odsEntity->getProperty($sapField) === null){
                    $dataTable->getError()->addColumnError('Unknown field '.$sapField.' maybe need a hulk/update-view/{display}');
                    continue;
                }*/

                $sapQuote = ('Edm.Int32' === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Decimal'
                    === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Double' ===
                    $odsEntity->getProperty($sapField)->getFieldType()) ? "" : "'";
                $sapDatetime = $odsEntity->getProperty($sapField)->getFieldType() === 'Edm.DateTime' ? 'datetime' : null;
                $min = explode('|', $value)[0];
                $max = explode('|', $value)[1];

                if ($min != null && $max != null) {
                    $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime.$sapQuote . $min . $sapQuote . ' and ');
                    $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime.$sapQuote . $max . $sapQuote . $filterOperator);
                } elseif ($min != null) {
                    $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime.$sapQuote . $min . $sapQuote);
                } elseif ($max != null) {
                    $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime.$sapQuote . $max . $sapQuote . $filterOperator);
                }

                if ($min != null || $max != null) {
                    $parameters->addRawFilter($rawFilter);
                }

            }
        }
    }

    /**
     * @param Display $dataTable
     * @param Parameters $parameters
     * @return void
     * @throws \Exception
     */
    private function addPreFilter(Display $dataTable, Parameters $parameters)
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