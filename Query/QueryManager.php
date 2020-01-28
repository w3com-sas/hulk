<?php

namespace W3com\HulkBundle\Query;

use Doctrine\Common\Annotations\AnnotationException;
use ReflectionException;
use Symfony\Component\HttpFoundation\ParameterBag;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\Parameters\Clause;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Form\DisplayType;
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
use function GuzzleHttp\Psr7\str;

class QueryManager
{

    /** @var BoomManager */
    private $boom;

    /** @var Entity */
    private $appEntity;

    /*** @var BoomGenerator */
    private $generator;

    /**
     * @param BoomManager $boom
     * @param BoomGenerator $generator
     */
    public function __construct(BoomManager $boom, BoomGenerator $generator)
    {
        $this->boom = $boom;
        $this->generator = $generator;
    }

    /**
     * @param Display $display
     * @param array $requestParams
     * @param null $top
     * @return array
     * @throws ReflectionException
     */
    public function createDataTableQuery(Display $display, $requestParams = [], $top = null)
    {
        $this->appEntity = $this->generator->getAppInspector()->getEntity($display->getCalcView());
        $display->getError()->setClassExist(true);

        try {
            $repo = $this->boom->getRepository($display->getEntityName());
        } catch (EntityNotFoundException $e) {
            $display->getError()->setClassExist(false);
            return null;
        }
        $params = $repo->createParams();

        if ($display->isFilter) {
            $this->addSelectForFilters($display, $params);
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

    private function addSelectProperty($fieldName, Parameters $params)
    {
        if ($this->appEntity->getProperty($fieldName) !== null) {
            $params->addSelect($this->appEntity->getProperty($fieldName)->getName());
        }
    }

    /**
     * @param Display $dataTable
     * @param Parameters $params
     * @throws \Exception
     */
    private function addSelectForColumns(Display $dataTable, Parameters $params)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {
            $this->addSelectProperty($column->getFieldName(), $params);
            $this->addSelectProperty($column->getIconFieldName(), $params);
            $this->addSelectProperty($column->getLabelFieldName(), $params);
            $this->addSelectProperty($column->getRenderFieldName(), $params);
            if ($column->getCellAction() != null && $this->appEntity->getProperty($column->getCellAction()->getRenderFieldName()) !== null) {
                $this->addSelectProperty($column->getCellAction()->getRenderFieldName(), $params);
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

            if ($key === 'SEARCH') {
                $this->addGlobalSearchFilter($value, $parameters, $display);
                break;
            }

            if ($this->appEntity->getProperty($key) !== null) {

                $paramsExist = true;

                $parameters->addFilter($this->appEntity->getProperty($key)->getName(), $value,
                    Clause::EQUALS, Clause:: AND);

                unset($arrayGetParams[$key]);

            }
        }

        foreach ($arrayGetParams as $key => $value) {


            if (substr($key, 0, strlen(UrlManager::INTERVAL_URL_KEY)) === UrlManager::INTERVAL_URL_KEY) {

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

    private function addGlobalSearchFilter($value, Parameters $parameters, Display $display)
    {
        $property = $display->getEntity()->getProperty(DisplayType::FIELD_GLOBAL_SEARCH);
        if ($property instanceof Property) {
            $parameters->addFilter($property->getName(), $value,
                Clause::SUBSTRING_OF, null, Clause::TO_LOWER);
        }
    }

}