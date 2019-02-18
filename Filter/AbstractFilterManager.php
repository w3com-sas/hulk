<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

abstract class AbstractFilterManager
{
    protected function addHidenColumn(Filter $filter, DataTable $dataTable)
    {
        $newCol = new Column();
        $newCol->setActive('Y');
        $newCol->setHidden(true);
        $newCol->setLabel($filter->getLabel());
        $newCol->setFieldName($filter->getField());
        $dataTable->addColumn($newCol);
    }

    protected function isColumnExist(Filter $filter, DataTable $dataTable)
    {
        /** @var Column $column */
        if (!empty($dataTable->getColumns())){
            foreach ($dataTable->getColumns() as $column){

                if ($filter->getField() === $column->getFieldName()){
                    return true;
                }

            }
        }
        return false;
    }
}