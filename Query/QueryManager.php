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
     * @throws ReflectionException
     *
     * @return array
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
        $this->addDefaultOrder($display, $params);
        null === $top ? $params->setTop($display->getMaxLength()) : $params->setTop($top);

        return $repo->findAll($params);
    }

    public function getResultLength($entityName, $routeParams, $display)
    {
        $repo = $this->boom->getRepository($entityName);

        $params = $repo->createParams();
        if (count($routeParams) > 0) {
            foreach ($routeParams as $key => $value) {
                if ('SEARCH' === $key) {
                    $this->addGlobalSearchFilter($value, $params, $display);
                    break;
                }
                if (null !== $this->appEntity->getProperty($key)) {
                    $paramsExist = true;

                    $params->addFilter($this->appEntity->getProperty($key)->getName(), $value,
                        Clause::EQUALS, Clause:: AND);
                }
            }
        }

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
     * @param array $getRequestParams
     *
     * @throws Exception
     */
    private function addGetParamsRequest(Display $display, $getRequestParams, Parameters $parameters)
    {
        $odsInspector = $this->generator->getOdsInspector();
        $odsInspector->initEntities();

        $odsEntity = $odsInspector->getEntity($display->getCalcView());

        if ($getRequestParams instanceof ParameterBag) {
            $arrayGetParams = $getRequestParams->all();
        } else {
            $arrayGetParams = $getRequestParams;
        }

        $paramsExist = false;

        foreach ($arrayGetParams as $key => $value) {
            if ('SEARCH' === $key) {
                $this->addGlobalSearchFilter($value, $parameters, $display);
                break;
            }

            if (null !== $this->appEntity->getProperty($key)) {
                $paramsExist = true;

                $parameters->addFilter($this->appEntity->getProperty($key)->getName(), $value,
                    Clause::EQUALS, Clause:: AND);

                unset($arrayGetParams[$key]);
            }
        }

        foreach ($arrayGetParams as $key => $value) {
            if (UrlManager::INTERVAL_URL_KEY === substr($key, 0, strlen(UrlManager::INTERVAL_URL_KEY))) {
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
                    $odsEntity->getProperty($sapField)->getFieldType()) ? '' : "'";
                $sapDatetime = 'Edm.DateTime' === $odsEntity->getProperty($sapField)->getFieldType() ? 'datetime' : null;
                $min = explode('|', $value)[0];
                $max = explode('|', $value)[1];

                if (null != $min && null != $max) {
                    $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime.$sapQuote.$min.$sapQuote.' and ');
                    $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime.$sapQuote.$max.$sapQuote.$filterOperator);
                } elseif (null != $min) {
                    $rawFilter .= sprintf(Clause::GREATER_THAN, $sapField, $sapDatetime.$sapQuote.$min.$sapQuote);
                } elseif (null != $max) {
                    $rawFilter .= sprintf(Clause::LOWER_THAN, $sapField, $sapDatetime.$sapQuote.$max.$sapQuote.$filterOperator);
                }

                if (null != $min || null != $max) {
                    $parameters->addRawFilter($rawFilter);
                }
            }
        }
    }

    /**
     * @throws Exception
     *
     * @return void
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
