<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;

class SingleFilterManager extends AbstractFilterManager
{

    public function manageSingleFilters(DataTable $dataTable, array $data)
    {
        $this->addValues($dataTable, $data);
        return $dataTable;
    }

    // Très gros traitement pour ajouter les données aux filtres...
    public function addValues(DataTable $dataTable, $boomObjs)
    {

        /** @var Filter $filter */
        foreach ($dataTable->getFilters() as $filter) {

            if ($filter->getType() === Filter::TYPE_SINGLE) {
                if ($filter->getActive() == 'Y' && !$this->isColumnExist($filter, $dataTable)) {
                    $this->addHidenColumn($filter, $dataTable);
                }

                // Can remove choice
                $filter->addValue(null);
                foreach ($boomObjs as $boomObj) {

                    foreach ((array)$boomObj as $property => $value) {

                        // Need to substring because the cast add characters (proteted property)
                        $realProperty = substr($property, 3);

                        if ($realProperty == strtolower($filter->getFieldName())) {
                            $filter->addValue($value);
                        }
                    }
                }
            }
        }
    }
}