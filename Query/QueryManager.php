<?php

namespace W3com\HulkBundle\Query;

use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\ParameterBag;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\Parameters\Clause;
use W3com\BoomBundle\Parameters\Parameters;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Model\CellAction;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Error;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Url\UrlManager;

class QueryManager
{
    /** @var BoomManager */
    private $boom;

    /** @var Entity */
    private $appEntity;

    /*** @var BoomGenerator */
    private $generator;

    public function __construct(BoomManager $boom, BoomGenerator $generator)
    {
        $this->boom = $boom;
        $this->generator = $generator;
    }

    /**
     * @param array $requestParams
     * @param null $top
     *
     * @return array
     * @throws ReflectionException
     *
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

        // Don't make request if no filter
        if ($display->isFilter && count($display->getFilters()) > 0) {
            $this->addSelectForFilters($display, $params);
            return $repo->findAll($params);
        } elseif ($display->isFilter && count($display->getFilters()) === 0) {
            return [];
        }

        $this->addSelectForColumns($display, $params);
        $this->addSelectForFilters($display, $params);
        $this->addSelectForLink($display, $params);
        $this->addGetParamsRequest($display, $requestParams, $params);
        $this->addPreFilter($display, $params);
        $this->addDefaultOrder($display, $params);
        null === $top ? $params->setTop($display->getMaxLength()) : $params->setTop($top);

        return $repo->findAll($params);
    }

    public function getResultLength($entityName, $routeParams, $display)
    {
        $repo = $this->boom->getRepository($entityName);
        $params = $repo->createParams();
        $this->buildParametersFromGetParams($display, $routeParams, $params);
        return $repo->count($params);
    }

    private function addSelectProperty($fieldName, Parameters $params)
    {
        if (null !== $this->appEntity->getProperty($fieldName)) {
            $params->addSelect($this->appEntity->getProperty($fieldName)->getName());
        }
    }

    private function addDefaultOrder(Display $dataTable, Parameters $params)
    {
        if (count($dataTable->getDefaultOrder()) > 0) {
            $property = $this->appEntity->getProperty($dataTable->getDefaultOrder()['FieldName'])->getName();
            $params->addOrder($property, strtolower($dataTable->getDefaultOrder()['Direction']));
        }
    }

    /**
     * @throws Exception
     */
    private function addSelectForColumns(Display $dataTable, Parameters $params)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {
            $this->addSelectProperty($column->getFieldName(), $params);
            $this->addSelectProperty($column->getIconFieldName(), $params);
            $this->addSelectProperty($column->getLabelFieldName(), $params);
            $this->addSelectProperty($column->getRenderFieldName(), $params);
            if (null != $column->getCellAction() && null !== $this->appEntity->getProperty($column->getCellAction()->getRenderFieldName())) {
                $this->addSelectProperty($column->getCellAction()->getRenderFieldName(), $params);
            }
        }
    }

    /**
     * This function allow Filter on hidden column.
     *
     * @throws Exception
     */
    private function addSelectForFilters(Display $display, Parameters $params)
    {
        if (!empty($display->getFilters())) {
            /** @var Filter $filter */
            foreach ($display->getFilters() as $filter) {
                if (null === $this->appEntity->getProperty($filter->getFieldName())) {
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
     * @throws Exception
     */
    private function addSelectForLink(Display $dataTable, Parameters $params)
    {
        if (!empty($dataTable->getColumns())) {
            /** @var Column $column */
            foreach ($dataTable->getColumns() as $column) {
                if (null !== $column->getCellAction()) {
                    if (CellAction::FUNCTION_DISPLAY_LINK == $column->getCellAction()->getFunctionName()
                        || CellAction::FUNCTION_LINK == $column->getCellAction()->getFunctionName()) {
                        foreach ($column->getCellAction()->getParams() as $fieldKey => $targetFieldKey) {
                            if (null !== $this->appEntity->getProperty($fieldKey)) {
                                $params->addSelect($this->appEntity->getProperty($fieldKey)->getName());
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $getRequestParams
     *
     * @throws Exception
     */
    private function addGetParamsRequest(Display $display, $getRequestParams, Parameters $parameters)
    {
        if ($getRequestParams instanceof ParameterBag) {
            $arrayGetParams = $getRequestParams->all();
        } else {
            $arrayGetParams = $getRequestParams;
        }

        return $this->buildParametersFromGetParams($display, $arrayGetParams, $parameters);
    }

    private function buildParametersFromGetParams(Display $display, array $arrayGetParams, Parameters $parameters)
    {
        $odsInspector = $this->generator->getOdsInspector();
        $odsInspector->initEntities();
        $odsEntity = $odsInspector->getEntity($display->getCalcView());

        $paramsExist = false;

        foreach ($arrayGetParams as $key => $value) {
            if ('SEARCH' === $key) {
                $paramsExist = true;
                $this->addGlobalSearchFilter($value, $parameters, $display);
                continue;
            }

            if (null !== $this->appEntity->getProperty($key)) {
                $paramsExist = true;
                $parameters->addFilter($this->appEntity->getProperty($key)->getName(), $value,
                    Clause::EQUALS, Clause:: AND);
            }
        }

        $intervalParams = array_filter($arrayGetParams, function ($value, $key) {
            return UrlManager::INTERVAL_URL_KEY === substr($key, 0, strlen(UrlManager::INTERVAL_URL_KEY));
        }, ARRAY_FILTER_USE_BOTH);

        // Has classic filter, need to begin raw filter with and clause
        $rawFilter = $paramsExist ? ' and ' : '';

        foreach ($intervalParams as $key => $value) {
            // Only the last doesn't have and clause
            $filterOperator = array_key_last($intervalParams) === $key ? '' : Clause::AND;

            $sapField = substr($key, strlen(UrlManager::INTERVAL_URL_KEY));
            $sapQuote = ('Edm.Int32' === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Decimal'
                === $odsEntity->getProperty($sapField)->getFieldType() || 'Edm.Double' ===
                $odsEntity->getProperty($sapField)->getFieldType()) ? '' : "'";
            $sapDatetime = 'Edm.DateTime' === $odsEntity->getProperty($sapField)->getFieldType() ? 'datetime' : null;

            $min = explode('|', $value)[0];
            $max = explode('|', $value)[1];


            if (null != $min && null != $max) {
                $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime . $sapQuote . $min . $sapQuote . ' and ');
                $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime . $sapQuote . $max . $sapQuote . $filterOperator);
            } elseif (null != $min) {
                $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime . $sapQuote . $min . $sapQuote.$filterOperator);
            } elseif (null != $max) {
                $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime . $sapQuote . $max . $sapQuote . $filterOperator);
            }
        }

        if ($rawFilter !== ' and ' && $rawFilter !== '') {
            $parameters->addRawFilter($rawFilter);
        }

        return $parameters;
    }

    /**
     * @return void
     * @throws Exception
     *
     */
    private function addPreFilter(Display $display, Parameters $parameters)
    {
        /** @var Filter $filter */
        foreach ($display->getFilters() as $filter) {
            if (Filter::TYPE_PRE_FILTER === $filter->getType()) {
                foreach ($filter->getParams() as $field => $value) {
                    if (null !== $this->appEntity->getProperty($field)) {
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
        $property = $display->getEntity()->getProperty(Display::FIELD_GLOBAL_SEARCH);
        if ($property instanceof Property) {
            $arr = explode(' ', $value);
            foreach ($arr as $subValue) {
                $parameters->addFilter($property->getName(), $subValue,
                    Clause::SUBSTRING_OF, null, Clause::TO_LOWER);
            }
        }
    }
}
