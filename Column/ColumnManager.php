<?php

namespace W3com\HulkBundle\Column;


use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;

class ColumnManager
{

    public function initColumns(DataTable $dataTable, $data)
    {
        $this->adaptColumnsWithData($dataTable, $data);
        $this->addCheckboxColumn($dataTable);
        return $dataTable;
    }

    /**
     * @param DataTable $dataTable
     * @param array $data
     */
    private function adaptColumnsWithData(DataTable $dataTable, array $data)
    {

        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {

            // if column is linked to property of entity
            foreach ((array)$data[0] as $property => $value) {

                $realProperty = substr($property, 3);

                if ($realProperty == strtolower($column->getFieldName())) {
                    $column->setActive('Y');
                }
            }
        }
    }

    private function addCheckboxColumn(DataTable $dataTable)
    {
        // If global action, need to add column with checkbox for selected table.
        if (!empty($dataTable->getGlobalActions())){
            $column = new Column();
            $column->setType('checkBox');
            $column->setLabel(null);
            $column->setActive('Y');
            $column->setFieldName(null);
            $dataTable->addColumn($column);
        }
        return $dataTable;

    }


}