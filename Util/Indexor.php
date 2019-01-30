<?php

namespace W3com\HulkBundle\Util;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class Indexor
{
    public function addIndex(DataTable $dataTable)
    {
        $this->addColumnsIndex($dataTable);
        $this->addFiltersIndex($dataTable);
        return $dataTable;
    }

    private function addColumnsIndex(DataTable $dataTable)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {
            if ($column->getActive() == 'Y') {
                if (!isset($i)) {
                    $i = 0;
                }
                $column->setIndex($i);
                $i++;
            }
        }
    }

    private function addFiltersIndex(DataTable $dataTable)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column){

            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter){

                if ($filter->getField() == $column->getFieldName()){
                    $filter->setIndex($column->getIndex());
                }
            }
        }
    }
}