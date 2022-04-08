<?php

namespace W3com\HulkBundle\Filter;

use DateTime;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

class SingleFilterManager extends AbstractFilterManager
{
    public function manageSingleFilters(Display $dataTable): Display
    {
        $this->addValues($dataTable);

        return $dataTable;
    }

    public function sortDateAsc($x, $y): int
    {
        return $this->sortDate($x, $y, 'asc');
    }

    public function sortDateDesc($x, $y): int
    {
        return $this->sortDate($x, $y, 'desc');
    }

    public function sortDate($x, $y, $order): int
    {
        $firstValue = 'asc' === $order ? 1 : -1;
        $secondValue = 'asc' === $order ? -1 : 1;

        if (null == $x) {
            return $secondValue;
        } elseif (null == $y) {
            return 0;
        }
        $stampX = DateTime::createFromFormat('d/m/Y', $x)->getTimestamp();
        $stampY = DateTime::createFromFormat('d/m/Y', $y)->getTimestamp();

        if ($stampX > $stampY) {
            return $firstValue;
        } elseif ($stampX < $stampY) {
            return $secondValue;
        }

        return 0;
    }

    public function addValues(Display $dataTable)
    {
        if (!empty($dataTable->getFilters())) {
            /** @var Filter $filter */
            foreach ($dataTable->getFilters() as $filter) {
                if (Filter::TYPE_SINGLE === $filter->getType()) {
                    if ('Y' == $filter->getActive() && !$this->isColumnExist($filter, $dataTable)) {
                        $this->addHidenColumn($filter, $dataTable);
                    }

                    $values = [];
                    foreach ($dataTable->getData() as $line) {
                        foreach ($line as $property => $value) {
                            if ($property == $filter->getFieldName()) {
                                if (false !== DateTime::createFromFormat('d/m/Y', $value)) {
                                    $isDate = true;
                                }
                                // Remove null values : select no support
                                if (null != $value) {
                                    $values[$value] = $value;
                                }
                            }
                        }
                    }

                    if (isset($isDate)) {
                        if (Filter::ORDER_ASC === $filter->getOrder()) {
                            usort($values, [$this, 'sortDateAsc']);
                        } else {
                            usort($values, [$this, 'sortDateDesc']);
                        }
                        $values = $this->formatValuesForChoices($values);
                        unset($isDate);
                    }
                    $filter->setValues(array_merge(['' => ''], $values));
                }
            }
        }
    }

    private function formatValuesForChoices(array $values): array
    {
        $fValues = [];
        foreach ($values as $value) {
            if (!empty($value)) {
                $fValues[$value] = $value;
            }
        }

        return $fValues;
    }
}
