<?php

namespace W3com\HulkBundle\Util;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Model\GlobalAction;

class Indexor
{
    public function addIndex(DataTable $dataTable)
    {
        $this->addColumnsIndex($dataTable);
        $this->addFiltersIndex($dataTable);
        $this->addGlobalActionIndex($dataTable);
        return $dataTable;
    }

    private function addColumnsIndex(DataTable $dataTable)
    {
        /** @var Column $column */
        $i = 0;

        foreach ($dataTable->getColumns() as $column) {
            if ($column->getActive() == 'Y') {
                $column->setIndex($i);
                $i++;
            }
        }
    }

    private function addFiltersIndex(DataTable $dataTable)
    {
        if (!empty($dataTable->getFilters())){
            /** @var Column $column */
            foreach ($dataTable->getColumns() as $column){

                /** @var Filter $filter */
                foreach ($dataTable->getFilters() as $filter){

                    if ($filter->getFieldName() == $column->getFieldName()){
                        $filter->setIndex($column->getIndex());
                    }
                }
            }
        }

    }

    private function addGlobalActionIndex(DataTable $dataTable)
    {
        $i=1;
        /** @var GlobalAction $globalAction */
        foreach ($dataTable->getGlobalActions() as $globalAction){
            $globalAction->setIndex($i);
            $i++;
        }
    }
}