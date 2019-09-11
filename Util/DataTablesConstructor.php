<?php

namespace W3com\HulkBundle\Util;

use W3com\HulkBundle\Model\CellAction;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Config;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Model\GlobalAction;

class DataTablesConstructor
{
    private $boom;

    public function __construct($boom)
    {
        $this->boom = $boom;
    }

    public function hydrateDataTable($file, Display $dataTable)
    {
        if ($dataTable->getError()->isFileExist()) {

            $decodedJson = json_decode($file, true);

            if ($decodedJson === null){

                $dataTable->getError()->setFileIsBroken(true);

            } else {
                foreach ($decodedJson as $key => $value) {
                    switch ($key) {
                        case Display::FIELD_CALCVIEW:
                            $dataTable->setCalcView($value);
                            break;
                        case Display::FIELD_GLOBAL_ACTION:
                            $this->hydrateGlobalAction($dataTable, $value);
                            break;
                        case Display::FIELD_COLUMNS:
                            $this->hydrateColumns($dataTable, $value);
                            break;
                        case Display::FIELD_FILTERS:
                            $this->hydrateFilters($dataTable, $value);
                            break;
                        case Display::FIELD_PAGE_LENGTH:
                            $dataTable->setPageLength(intval($value));
                            break;
                        case Display::FIELD_DISPLAY_NAME:
                            $dataTable->setDisplayName($value);
                            break;
                        case Display::FIELD_MENU_CONFIG:
                            $dataTable->setMenuConfig($value);
                            break;
                            case Display::FIELD_MENU_NAME;
                            $dataTable->setMenuName($value);
                            break;
                    }
                }
                if ($dataTable->getPageLength() === null) {
                    $dataTable->setPageLength(10000);
                }
            }
        }
        return $dataTable;
    }

    private function hydrateColumns(Display $dataTable, $columns)
    {
        foreach ($columns as $dataColumn) {
            $column = new Column();
            foreach ($dataColumn as $field => $value) {
                switch ($field) {
                    case Column::FIELD_LABEL:
                        $column->setLabel($value);
                        break;
                    case Column::FIELD_FIELDNAME:
                        $column->setFieldName($value);
                        break;
                    case Column::FIELD_ICON_FIELDNAME:
                        $column->setIconFieldName($value);
                        break;
                    case Column::FIELD_LABEL_FIELDNAME:
                        $column->setLabelFieldName($value);
                        break;
                    case Column::FIELD_TYPE:
                        $column->setType($value);
                        break;
                    case Column::FIELD_CELL_ACTION:
                        $column->setCellAction($this->hydrateCellAction($value));
                        break;
                    case Column::FIELD_WIDTH:
                        $column->setWidth($value);
                        break;
                    case Column::FIELD_HIDDEN:
                        $column->setHidden($value);
                        break;
                    case Column::FIELD_ORDERABLE:
                        $column->setOrderable(true);
                        break;

                }
            }
            $dataTable->addColumn($column);
        }
    }

    private function hydrateFilters(Display $dataTable, $filters)
    {
        foreach ($filters as $jsonFilter) {
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
                    case Filter::FIELD_PARAMS:
                        $filter->setParams($value);
                        break;
                    case Filter::FIELD_ORDER:
                        $filter->setOrder($value);
                        break;

                }
            }
            $dataTable->addFilter($filter);
        }
    }

    private function hydrateCellAction(array $dataAction)
    {
        $action = new CellAction();
        foreach ($dataAction as $field => $value) {
            switch ($field) {
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
                    break;
                case CellAction::FIELD_ICON_FIELDNAME:
                    $action->setIconFieldName($value);
                    break;
                case CellAction::FIELD_PARAMS:
                    foreach ($value as $fieldKey => $targetFieldKey) {
                        $action->addParam($fieldKey, $targetFieldKey);
                    }
                    break;
            }
        }
        return $action;
    }

    private function hydrateGlobalAction(Display $dataTable, array $dataGlobalActions)
    {
        foreach ($dataGlobalActions as $globalAction) {
            $newGlobalAction = new GlobalAction();
            foreach ($globalAction as $field => $value) {
                switch ($field) {
                    case GlobalAction::FIELD_LABEL:
                        $newGlobalAction->setLabel($value);
                        break;
                    case GlobalAction::FIELD_TYPE:
                        $newGlobalAction->setType($value);
                        break;
                    case GlobalAction::FIELD_CONFIG:
                        if(array_key_exists('Entity',$value) && array_key_exists('TargetField',$value)){
                            $moreHydratation = $this->hydrateConfigWithBoom($value['Entity'],$value['TargetField']);
                            $value = array_merge($value,$moreHydratation);
                        }
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
        foreach ($arrayConfig as $field => $value) {

            switch ($field) {
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
                case Config::FIELD_URL:
                    $newConfig->setUrl($value);
                    break;
                case Config::FIELD_NAME:
                    $newConfig->setName($value);
                    break;
                case Config::FIELD_LABEL:
                    $newConfig->setLabel($value);
                    break;
                case Config::FIELD_TARGET_DESCRIPTION:
                    $newConfig->setTargetDescription($value);
                    break;
                case Config::FIELD_TARGET_CHOICES:
                    $newConfig->setTargetChoices($value);
                    break;
            }
        }
        return $newConfig;
    }

    private function hydrateConfigWithBoom($entity,$fieldname)
    {
        $entityUtil = $this->boom->getRepository($entity);
        if($entityUtil == null) return [];

        $instanceName = '\\App\\HanaENtity\\'.$entity;
        $instance = new $instanceName();

        $property = $instance->getPropertyByColumn($fieldname);
        $description = $instance->getDescriptionByProperty($property);
        $type = $instance->getTypeByField($property);
        $choices = $instance->getChoicesByProperty($property);

        return [
            'TargetProperty' => $property,
            'TargetDescription' => $description,
            'TargetType' => $type,
            'TargetChoices' => $choices,
        ];

    }
}