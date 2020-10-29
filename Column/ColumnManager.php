<?php

namespace W3com\HulkBundle\Column;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;

class ColumnManager
{
    public function initColumns(Display $dataTable)
    {
        $this->adaptColumnsWithData($dataTable);
        $this->addCheckboxColumn($dataTable);

        return $dataTable;
    }

    private function adaptColumnsWithData(Display $display)
    {
        /** @var Column $column */
        foreach ($display->getColumns() as $column) {
            foreach ($display->getFirstLineData() as $property => $value) {
                if (in_array($property, [$column->getFieldName(), $column->getIconFieldName(), $column->getLabelFieldName(), $column->getRenderFieldName()])) {
                    $column->setActive('Y');
                    continue;
                }

                if ($column->hasCellAction()) {
                    if ($column->getCellAction()->getFunctionName().$column->getCellAction()->getTargetEntity() == $property) {
                        $column->setActive('Y');
                        continue;
                    }
                }

                if (Column::COL_TYPE_CALL_FUNCTION === $column->getType()) {
                    $column->setActive('Y');
                    continue;
                }

                if ('Y' !== $column->getActive()) {
                    $column->setActive('N');
                }
            }
        }
    }

    private function addCheckboxColumn(Display $dataTable)
    {
        // If global action, need to add column with checkbox for selected table.
        if (!empty($dataTable->getGlobalActions())) {
            $column = new Column();
            $column->setType(Column::COL_TYPE_CHECKBOX);
            $column->setLabel(null);
            $column->setActive('Y');
            $column->setFieldName(null);
            $columns = $dataTable->getColumns();
            array_unshift($columns, $column);
            $dataTable->setColumns($columns);
        }

        return $dataTable;
    }
}
