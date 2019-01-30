<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class FilterManager
{

    public function initFilters(DataTable $dataTable, $data)
    {
        $this->addValues($dataTable, $data);
        $this->adaptFilters($dataTable);
        return $dataTable;
    }

    // Très gros traitement pour ajouter les données aux filtres...
    private function addValues(DataTable $dataTable, $boomObjs)
    {
        /** @var Filter $filter */
        foreach ($dataTable->getFilters() as $filter) {

            if ($filter->getActive() == 'Y'){
                $this->addColumn($filter, $dataTable);
            }

            foreach ($boomObjs as $boomObj) {

                foreach ((array)$boomObj as $property => $value)
                {
                    $realProperty = substr($property, 3);

                    if ($realProperty == strtolower($filter->getField())) {
                        $filter->addValue($value);
                    }
                }
            }
        }
    }

    private function adaptFilters(DataTable $dataTable)
    {
        /** @var Filter $filter */
        foreach ($dataTable->getFilters() as $filter) {
            $this->isColumnExist($dataTable, $filter);
        }
    }

    private function isColumnExist(DataTable $dataTable, Filter $filter)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {
            if ($filter->getField() === $column->getFieldName()) {
                $filter->setColumnExist(true);
                return $filter;
            }
        }
        $filter->setColumnExist(false);
        return $filter;
    }

    private function addColumn(Filter $filter, DataTable $dataTable)
    {
        $newCol = new Column([]);
        $newCol->setActive('Y');
        $newCol->setHidden(true);
        $newCol->setViewName($filter->getLabel());
        $newCol->setFieldName($filter->getField());
        $dataTable->addColumn($newCol);
    }

}