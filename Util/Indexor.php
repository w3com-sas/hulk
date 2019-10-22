<?php

namespace W3com\HulkBundle\Util;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Model\GlobalAction;

class Indexor
{
    public function addIndex(Display $dataTable)
    {
        $this->addColumnsIndex($dataTable);
        $this->addFiltersIndex($dataTable);
        $this->addGlobalActionIndex($dataTable);
        $this->addIconFieldNameIndex($dataTable);
        return $dataTable;
    }

    private function addColumnsIndex(Display $dataTable)
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

    private function addFiltersIndex(Display $dataTable)
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

    private function addGlobalActionIndex(Display $dataTable)
    {
        $i=1;

        if (!empty($dataTable->getGlobalActions())){
            /** @var GlobalAction $globalAction */
            foreach ($dataTable->getGlobalActions() as $globalAction){
                $globalAction->setIndex($i);
                $i++;
            }
        }

    }

    private function addIconFieldNameIndex(Display $dataTable)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column){

            if ($column->getCellAction() !== null && $column->getCellAction()->getIconFieldName() !== null){

                /** @var Column $toCompareColumn */
                foreach ($dataTable->getColumns() as $toCompareColumn){

                    if ($toCompareColumn->getFieldName() === $column->getCellAction()->getIconFieldName()){
                        $column->getCellAction()->setIconColumnIndex($toCompareColumn->getIndex());
                    }

                }

            }

        }
    }
}