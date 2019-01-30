<?php

namespace W3com\HulkBundle\Column;


use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;

class ColumnManager
{

    public function initColumns(DataTable $dataTable, $data)
    {
        $this->adaptColumnsWithData($dataTable, $data);
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


}