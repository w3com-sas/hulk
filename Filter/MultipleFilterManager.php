<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class MultipleFilterManager extends AbstractFilterManager
{
    public function manageMultipleFilters(DataTable $dataTable)
    {
        if (!empty($dataTable->getFilters())){
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {
                if ($filter->getType() === Filter::TYPE_MULTIPLE || $filter->getType() === Filter::TYPE_MULTIPLE_DATE) {
                    if ($filter->getActive() == 'Y' && !$this->isColumnExist($filter, $dataTable)) {

                        $this->addHidenColumn($filter, $dataTable);
                    }
                }
            }
        }

    }
}