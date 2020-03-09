<?php

namespace W3com\HulkBundle\Util;

use phpDocumentor\Reflection\Types\Mixed_;
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

    /** @var Display */
    private $display;

    public function __construct(BoomManager $boom, AppInspector $appInspector, UrlGeneratorInterface $router, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->router = $router;
        $this->boom = $boom;
        $this->appInspector = $appInspector;
    }

    public function hydrate(Display $display, $json)
    {
        $this->display = $display;

        if ($this->display->getError()->isFileExist()) {
            $decodedJson = json_decode($json, true);
            if ($decodedJson === null) {
                $this->display->getError()->setFileIsBroken(true);
            } else {
                foreach ($decodedJson as $key => $value) {
                    $this->hydrateDisplay($key, $value);
                }
                if ($this->display->getPageLength() === null) {
                    $this->display->setPageLength(10000);
                }
            }
        }
        return $this->display;
    }

    public function hydrateDisplay($key, $value)
    {
        switch ($key) {
            case Display::FIELD_CALCVIEW:
                $this->display->setCalcView($value);
                $this->display->setEntity($this->appInspector->getEntity($value));
                if ($this->display->getEntity() === null) {
                    $this->display->getError()->addEntityErrors('Impossible de trouver l\'entité ' . $value);
                }
                break;
            case Display::FIELD_GLOBAL_ACTION:
                $this->hydrateGlobalAction($value);
                break;
            case Display::FIELD_COLUMNS:
                $this->hydrateColumns($value);
                break;
            case Display::FIELD_FILTERS:
                $this->hydrateFilters($value);
                break;
            case Display::FIELD_PAGE_LENGTH:
                $this->display->setPageLength(intval($value));
                break;
            case Display::FIELD_DISPLAY_NAME:
                $this->display->setDisplayName($value);
                break;
            case Display::FIELD_MENU_CONFIG:
                $this->display->setMenuConfig($value);
                break;
            case Display::FIELD_MENU_NAME;
                $this->display->setMenuName($value);
                break;
            case Display::FIELD_LABEL;
                $this->display->setLabel($value);
                break;
        }
    }

    private function hydrateColumns($columns)
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

                        if ($value === Column::COL_TYPE_UPDATE_SAP){
                            $additionalData = $this->hydrateConfigWithBoom($dataColumn['CellAction']['Entity'], $dataColumn['FieldName']);
                            if (array_key_exists('TargetChoices', $additionalData)){
                                $column->getConfig()->setTargetData($additionalData['TargetChoices']);
                            }
                        }

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
                    case Column::FIELD_RENDER_ELEMENT_OPTIONS:
                        $column->setRenderElementOptions($value);
                        break;
                    case GlobalAction::FIELD_CONFIG:
                        if ($value === Column::COL_TYPE_UPDATE_SAP){
                            $value = array_merge($value, $this->hydrateConfigWithBoom(
                                $this->display->getEntity()->getTable(),
                                $value['TargetField']
                            ));
                        }
                        $column->setConfig($this->hydrateConfig($value));
                        break;
                }
            }
            $this->display->addColumn($column);
        }
    }

    private function hydrateFilters($filters)
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
            $this->display->addFilter($filter);
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

    private function hydrateGlobalAction(array $dataGlobalActions)
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
                            $value = array_merge($value, $this->hydrateConfigWithBoom($value['Entity'], $value['TargetField']));
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
            $this->display->addGlobalAction($newGlobalAction);
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

    private function hydrateConfigWithBoom($entity, $fieldName)
    {
        // TODO Utilisé AppInspector via BoomGenerator pour deviner la classe avec la table
        try {
            $entityUtil = $this->boom->getRepository($entity);
        } catch (EntityNotFoundException $e) {
            $this->display->getError()->addEntityErrors('Impossible de trouver la table ' . $entity);
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