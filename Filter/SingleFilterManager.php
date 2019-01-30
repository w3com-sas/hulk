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

            if ($filter->getType() === Filter::TYPE_SINGLE){

                if ($filter->getActive() == 'Y'){
                    $this->addHidenColumn($filter, $dataTable);
                }

                foreach ($boomObjs as $boomObj) {

                    foreach ((array)$boomObj as $property => $value)
                    {
                        $realProperty = substr($property, 3);

                        if ($realProperty == strtolower($filter->getField())) {
                            $filter->addValue($value);
                        }
                    }
                }
            }
        }
    }
}