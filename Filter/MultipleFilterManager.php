<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class MultipleFilterManager extends AbstractFilterManager
{
    public function manageMultipleFilters(DataTable $dataTable)
    {
        /** @var Filter $filter */
        foreach ($dataTable->getFilters() as $filter) {
            if ($filter->getType() === Filter::TYPE_MULTIPLE) {
                if ($filter->getActive() == 'Y' && !$this->isColumnExist($filter, $dataTable)) {
                    $this->addHidenColumn($filter, $dataTable);
                }
            }
        }
    }
}