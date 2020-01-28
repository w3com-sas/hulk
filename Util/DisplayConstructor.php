<?php

namespace W3com\HulkBundle\Util;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\AppInspector;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Model\CellAction;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Config;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Model\GlobalAction;

class DisplayConstructor
{
    /** @var BoomManager */
    private $boom;

    /** @var AppInspector */
    private $appInspector;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(BoomManager $boom, BoomGenerator $boomGenerator, UrlGeneratorInterface $router, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->router = $router;
        $this->boom = $boom;
        $this->appInspector = $boomGenerator->getAppInspector();
    }

    public function hydrateDataTable($file, Display $display)
    {
        if ($display->getError()->isFileExist()) {
            $decodedJson = json_decode($file, true);
            if ($decodedJson === null) {
                $display->getError()->setFileIsBroken(true);
            } else {
                foreach ($decodedJson as $key => $value) {
                    switch ($key) {
                        case Display::FIELD_CALCVIEW:
                            $display->setCalcView($value);
                            $display->setEntity($this->appInspector->getEntity($value));
                            if ($display->getEntity() === null) {
                                $display->getError()->addEntityErrors('Impossible de trouver l\'entité ' . $value);
                            }
                            break;
                        case Display::FIELD_GLOBAL_ACTION:
                            $this->hydrateGlobalAction($display, $value);
                            break;
                        case Display::FIELD_COLUMNS:
                            $this->hydrateColumns($display, $value);
                            break;
                        case Display::FIELD_FILTERS:
                            $this->hydrateFilters($display, $value);
                            break;
                        case Display::FIELD_PAGE_LENGTH:
                            $display->setPageLength(intval($value));
                            break;
                        case Display::FIELD_DISPLAY_NAME:
                            $display->setDisplayName($value);
                            break;
                        case Display::FIELD_MENU_CONFIG:
                            $display->setMenuConfig($value);
                            break;
                        case Display::FIELD_MENU_NAME;
                            $display->setMenuName($value);
                            break;
                        case Display::FIELD_LABEL;
                            $display->setLabel($value);
                            break;
                    }
                }
                if ($display->getPageLength() === null) {
                    $display->setPageLength(10000);
                }
            }
        }
        return $display;
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
                    case Column::FIELD_RENDER_FIELDNAME:
                        $column->setRenderFieldName($value);
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
                    case Column::FIELD_PARAMS:
                        $column->setParams($value);
                        break;
                    case GlobalAction::FIELD_CONFIG:
                        $column->setConfig($this->hydrateConfig($value));
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
                    case Filter::FIELD_FIELD_NAME:
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
                case CellAction::FIELD_RENDER_TYPE:
                    $action->setRenderType($value);
                    break;
                case CellAction::FIELD_RENDER_VALUE:
                    $action->setRenderValue($value);
                    break;
                case CellAction::FIELD_RENDER_FIELDNAME:
                    $action->setRenderFieldName($value);
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
                        if (array_key_exists('Entity', $value) && array_key_exists('TargetField', $value)) {
                            $moreHydratation = $this->hydrateConfigWithBoom($value['Entity'], $value['TargetField'], $dataTable);
                            $value = array_merge($value, $moreHydratation);
                        }
                        $config = $this->hydrateConfig($value);
                        $newGlobalAction->setConfig($config);
                        break;
                    case GlobalAction::FIELD_COLOR:
                        $newGlobalAction->setColor($value);
                        break;
                    case GlobalAction::FIELD_ICON:
                        $newGlobalAction->setIcon($value);
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
                    try {
                        $value = $this->router->generate($value);
                    } catch (\Exception $e) {
                        $this->logger->warning($e->getMessage(), $e->getTrace());
                    }
                    $newConfig->setUrl($value);
                    break;
                case Config::FIELD_MESSAGE_CONFIRM:
                    $newConfig->setMessageConfirm($value);
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
                case Config::FIELD_USER_CONFIRM:
                    $newConfig->setUserConfirm($value);
                    break;

            }
        }
        return $newConfig;
    }

    private function hydrateConfigWithBoom($entity, $fieldName, Display $display)
    {
        // TODO Utilisé AppInspector via BoomGenerator pour deviner la classe avec la table
        try {
            $entityUtil = $this->boom->getRepository($entity);
        } catch (EntityNotFoundException $e) {
            $display->getError()->addEntityErrors('Impossible de trouver la table ' . $entity);
            return [];
        }

        if ($entityUtil == null) return [];

        $instanceName = '\\App\\HanaEntity\\' . $entity;
        $instance = new $instanceName();

        $property = $instance->getPropertyByColumn($fieldName);
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