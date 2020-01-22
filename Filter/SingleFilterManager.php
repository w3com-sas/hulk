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

    public function sortDate($x, $y)
    {
        if ($x == null){
            return -1;
        } elseif ($y == null){
            return 0;
        }
        $stampX = \DateTime::createFromFormat('d/m/Y', $x)->getTimestamp();
        $stampY = \DateTime::createFromFormat('d/m/Y', $y)->getTimestamp();

        if ($stampX > $stampY) {
            return 1;
        } elseif ($stampX < $stampY) {
            return -1;
        } else {
            return 0;
        }
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

                    $values = [];
                    $values[""] = "";
                    foreach ($dataTable->getData() as $line) {

                        foreach ($line as $property => $value) {

                            if ($property == $filter->getFieldName()) {
                                if (\DateTime::createFromFormat('d/m/Y', $value) !== false){
                                    $isDate = true;
                                }
                                // Remove null values : select no support
                                $values[$value] = $value === null ? "" : $value;
                            }
                        }
                    }

                    if (isset($isDate)) {
                        usort($values, [$this, "sortDate"]);
                        $values = $this->formatValuesForChoices($values);
                        unset($isDate);
                    }
                    $filter->setValues($values);
                }
            }
        }
    }

    private function formatValuesForChoices(array $values)
    {
        $fValues= [];
        foreach ($values as $value){
            $fValues[$value] = $value;
        }
        return $fValues;
    }


}