<?php

namespace W3com\HulkBundle\Column;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;

class ColumnManager
{

    public function initColumns(DataTable $dataTable)
    {
        $this->adaptColumnsWithData($dataTable);
        $this->addCheckboxColumn($dataTable);
        return $dataTable;
    }

    /**
     * @param DataTable $dataTable
     */
    private function adaptColumnsWithData(DataTable $dataTable)
    {

        /** @var Column $column */
        if (!empty($dataTable->getColumns())) {

            foreach ($dataTable->getColumns() as $column) {

                // if column is linked to property of entity
                if (!empty($dataTable->getData())) {

                    foreach ($dataTable->getData()[0] as $property => $value) {

                        if ($property == $column->getFieldName()) {
                            $column->setActive('Y');
                        } elseif ($column->hasCellAction()) {

                            if ($column->getCellAction()->getFunctionName() . $column->getCellAction()->getTargetEntity()
                                == $property) {
                                $column->setActive('Y');
                            }

                        }

                        if ($column->getActive() !== 'Y') {
                            $column->setActive('N');
                        }
                    }
                }

            }
        }
    }

    private function addCheckboxColumn(DataTable $dataTable)
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