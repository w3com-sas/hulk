<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class SingleFilterManager extends AbstractFilterManager
{

    public function manageSingleFilters(DataTable $dataTable)
    {
        $this->addValues($dataTable);
        return $dataTable;
    }

    // Très gros traitement pour ajouter les données aux filtres...
    public function addValues(DataTable $dataTable)
    {

        if (!empty($dataTable->getFilters())){
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {

                if ($filter->getType() === Filter::TYPE_SINGLE) {
                    if ($filter->getActive() == 'Y' && !$this->isColumnExist($filter, $dataTable)) {
                        $this->addHidenColumn($filter, $dataTable);
                    }

                    // Can remove choice
                    $filter->addValue(null);
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