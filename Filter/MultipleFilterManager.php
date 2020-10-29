<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

class MultipleFilterManager extends AbstractFilterManager
{
    public function manageMultipleFilters(Display $dataTable)
    {
        if (!empty($dataTable->getFilters())) {
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {
                if (Filter::TYPE_MULTIPLE === $filter->getType() || Filter::TYPE_MULTIPLE_DATE === $filter->getType()) {
                    if ('Y' == $filter->getActive() && !$this->isColumnExist($filter, $dataTable)) {
                        $this->addHidenColumn($filter, $dataTable);
                    }
                }
            }
        }
    }
}
