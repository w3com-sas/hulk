<?php

namespace W3com\HulkBundle\Util;

use W3com\HulkBundle\Model\CellAction;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Config;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Model\GlobalAction;

class DataTablesConstructor
{
    public function hydrateDataTable($file, DataTable $dataTable)
    {
        if ($dataTable->getError()->isFileExist()){
            foreach (json_decode($file, true) as $key => $value) {
                switch ($key) {
                    case DataTable::FIELD_CALCVIEW:
                        $dataTable->setCalcView($value);
                        break;
                    case DataTable::FIELD_GLOBAL_ACTION:
                        $this->hydrateGlobalAction($dataTable, $value);
                        break;
                    case DataTable::FIELD_COLUMNS:
                        $this->hydrateColumns($dataTable, $value);
                        break;
                    case DataTable::FIELD_FILTERS:
                        $this->hydrateFilters($dataTable, $value);
                        break;
                }
            }
        }
        return $dataTable;
    }

    private function hydrateColumns(DataTable $dataTable, $columns)
    {
        foreach ($columns as $dataColumn){
            $column = new Column();
            foreach ($dataColumn as $field => $value) {
                switch ($field) {
                    case Column::FIELD_LABEL:
                        $column->setLabel($value);
                        break;
                    case Column::FIELD_FIELDNAME:
                        $column->setFieldName($value);
                        break;
                    case Column::FIELD_TYPE:
                        $column->setType($value);
                        break;
                    case Column::FIELD_CELL_ACTION:
                        $column->setCellAction($this->hydrateCellAction($value));
                }
            }
            $dataTable->addColumn($column);
        }
    }

    private function hydrateFilters(DataTable $dataTable, $filters)
    {
        foreach ($filters as $jsonFilter){
            $filter = new Filter();
            foreach ($jsonFilter as $field => $value) {
                switch ($field) {
                    case Filter::FIELD_FIELDNAME:
                        $filter->setFieldName($value);
                        break;
                    case Filter::FIELD_LABEL:
                        $filter->setLabel($value);
                        break;
                    case Filter::FIELD_TYPE:
                        $filter->setType($value);
                        break;
                }
            }
            $dataTable->addFilter($filter);
        }
    }

    private function hydrateCellAction(array $dataAction)
    {
        $action = new CellAction();
        foreach ($dataAction as $field => $value){
            switch ($field){
                case CellAction::FIELD_LABEL:
                    $action->setLabel($value);
                    break;
                case CellAction::FIELD_FUNCTION_NAME:
                    $action->setFunctionName($value);
                    break;
                case CellAction::FIELD_TARGET_ENTITY:
                    $action->setTargetEntity($value);
                    break;
                case CellAction::FIELD_ICON:
                    $action->setIcon($value);
            }
        }
        return $action;
    }

    private function hydrateGlobalAction(DataTable $dataTable, array $dataGlobalActions)
    {
        foreach ($dataGlobalActions as $globalAction){
            $newGlobalAction = new GlobalAction();
            foreach ($globalAction as $field => $value){
                switch ($field){
                    case GlobalAction::FIELD_LABEL:
                        $newGlobalAction->setLabel($value);
                        break;
                    case GlobalAction::FIELD_TYPE:
                        $newGlobalAction->setType($value);
                        break;
                    case GlobalAction::FIELD_CONFIG:
                        $config = $this->hydrateConfig($value);
                        $newGlobalAction->setConfig($config);
                        break;
                }
            }
            $dataTable->addGlobalAction($newGlobalAction);
        }
    }

    private function hydrateConfig($arrayConfig)
    {
        $newConfig = new Config();
        foreach ($arrayConfig as $field => $value){

            switch ($field){
                case Config::FIELD_ENTITY:
                    $newConfig->setEntity($value);
                    break;
                case Config::FIELD_ENTITY_KEY:
                    $newConfig->setEntityKey($value);
                    break;
                case Config::FIELD_TARGET_FIELD:
                    $newConfig->setTargetField($value);
                    break;
                case Config::FIELD_TARGET_DATA_TYPE:
                    $newConfig->setTargetDataType($value);
                    break;
                case Config::FIELD_TARGET_DATA:
                    $newConfig->setTargetData($value);
                    break;
            }
        }
        return $newConfig;
    }
}