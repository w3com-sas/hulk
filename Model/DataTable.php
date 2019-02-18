<?php

namespace W3com\HulkBundle\Model;


use W3com\BoomBundle\Generator\Model\Property;

class DataTable
{
    const FIELD_CALCVIEW = 'CalculationView';
    const FIELD_GLOBAL_ACTION = 'GlobalActions';
    const FIELD_FILTERS = 'Filters';
    const FIELD_COLUMNS = 'Columns';

    public function __construct()
    {
        $this->error = new Error();
    }

    /**
     * @var string
     */
    private $calcView;

    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var array
     */
    private $globalActions;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var array
     */
    private $data;

    /**
     * @var Error
     */
    private $error;

    /**
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function setCalcView($calcView)
    {
        return $this->calcView = $calcView;
    }

    /**
     * @return mixed
     */
    public function getCalcView()
    {
        return $this->calcView;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }


    /**
     * @param $entityFields
     * @return mixed
     *
     * Return fields who exist in entity and in Json File
     */
    public function getAvailableFields($entityFields)
    {
        $fields = [];
        /** @var Column $column */
        foreach ($this->columns as $column) {

            // Need to get property name to check it in entity
            /** @var Property $property */
            foreach ($entityFields as $property) {
                if ($property->getField() == $column->getFieldName()) {
                    $fields[$column->getFieldName()] = $property;
                }
            }
        }

        /** @var Filter $filter */
        foreach ($this->filters as $filter) {
            foreach ($entityFields as $property) {
                if ($property->getField() == $filter->getFieldName()) {
                    $fields[$filter->getFieldName()] = $property;
                }
            }
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getGlobalActions(): ?array
    {
        return $this->globalActions;
    }

    /**
     * @param GlobalAction $globalAction
     */
    public function addGlobalAction(GlobalAction $globalAction)
    {
        $this->globalActions[] = $globalAction;
    }

    /**
     * @return Error
     */
    public function getError(): Error
    {
        return $this->error;
    }

    public function getFilterByFieldName($fieldName)
    {
        /** @var Filter $filter */
        foreach ($this->filters as $filter){

            if ($filter->getFieldName() == $fieldName){
                return $filter;
            }
        }
        return false;
    }

    public function getColumnByFieldName($fieldName)
    {
        /** @var Column $column */
        foreach ($this->filters as $column){

            if ($column->getFieldName() == $fieldName){
                return $column;
            }
        }
        return false;
    }

}