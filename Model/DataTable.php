<?php

namespace W3com\HulkBundle\Model;

use W3com\BoomBundle\Generator\Model\Property;

class DataTable extends AbstractDataTable
{

    private $columns;

    private $filters;

    private $orders;

    private $data;

    /**
     * @param $columns
     */
    public function setColumns($columns)
    {
        foreach ($columns as $column){
            $this->columns[] = new Column($column);
        }
    }

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
     * @param mixed $filters
     */
    public function setFilters(array $filters): void
    {
        foreach ($filters as $filter){
            $this->filters[] = new Filter($filter, $this);
        }
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders(array $orders): void
    {
        $this->orders = $orders;
    }


    /**
     * @param $entityFields
     * @return mixed
     *
     * Return fields who exist in entity and in Json File
     *
     */
    public function getAvailableFields($entityFields)
    {
        $fields = [];
        /** @var Column $column */
        foreach ($this->columns as $column){

            // Need to get property name to check it in entity
            /** @var Property $property */
            foreach ($entityFields as $property){
                if ($property->getField() == $column->getFieldName()){
                    $fields[$column->getFieldName()] = $property;
                }
            }
        }

        /** @var Filter $filter */
        foreach ($this->filters as $filter){
            foreach ($entityFields as $property){
                if ($property->getField() == $filter->getField()){
                    $fields[$filter->getField()] = $property;
                }
            }
        }

        return $fields;
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


}