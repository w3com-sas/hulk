<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

abstract class AbstractFilterManager
{
    protected function addHidenColumn(Filter $filter, Display $dataTable)
    {
        $newCol = new Column();
        $newCol->setActive('Y');
        $newCol->setHidden(true);
        $newCol->setLabel($filter->getLabel());
        $newCol->setFieldName($filter->getFieldName());
        $dataTable->addColumn($newCol);
    }

    protected function isColumnExist(Filter $filter, Display $dataTable)
    {
        /* @var Column $column */
        if (!empty($dataTable->getColumns())) {
            foreach ($dataTable->getColumns() as $column) {
                if ($filter->getFieldName() === $column->getFieldName()) {
                    return true;
                }
            }
        }

        return false;
    }
}
