<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

class SingleFilterManager extends AbstractFilterManager
{

    public function manageSingleFilters(Display $dataTable)
    {
        $this->addValues($dataTable);
        return $dataTable;
    }

    public function addValues(Display $dataTable)
    {

        if (!empty($dataTable->getFilters())) {
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {

                if ($filter->getType() === Filter::TYPE_SINGLE) {

                    if ($filter->getActive() == 'Y' && !$this->isColumnExist($filter, $dataTable)) {
                        $this->addHidenColumn($filter, $dataTable);
                    }

                    // Can remove choice
                    $filter->addValue("");
                    foreach ($dataTable->getData() as $line) {

                        foreach ($line as $property => $value) {

                            if ($property == $filter->getFieldName()) {
                                $filter->addValue($value);
                            }
                        }
                    }
                }
            }
        }

    }
}