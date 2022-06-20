<?php

namespace W3com\HulkBundle\Model;

use Symfony\Component\HttpFoundation\ParameterBag;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Generator\Model\Property;

class Display
{
    const FIELD_CALCVIEW = 'CalculationView';
    const FIELD_DEFAULT_ORDER = 'DefaultOrder';
    const FIELD_GLOBAL_ACTION = 'GlobalActions';
    const FIELD_FILTERS = 'Filters';
    const FIELD_COLUMNS = 'Columns';
    const FIELD_PAGE_LENGTH = 'PageLength';
    const FIELD_DISPLAY_NAME = 'DisplayName';
    const FIELD_DISPLAY_NAMES = 'DisplayNames';
    const FIELD_MENU_CONFIG = 'MenuConfig';
    const FIELD_MENU_NAME = 'MenuName';
    const FIELD_LABEL = 'Label';
    const FIELD_GLOBAL_SEARCH = 'SEARCH';

    public $isFilter = false;

    /** @var string */
    private $label;

    /** @var string */
    private $filename;

    /** @var string */
    private $displayName;

    /** @var string */
    private $calcView;

    /** @var array */
    private $defaultOrder = [];

    /**
     * @var mixed
     */
    private $entity;

    /** @var array */
    private $globalActions;

    /** @var array */
    private $columns = [];

    /** @var array */
    private $filters = [];

    /** @var array */
    private $data = [];

    /** @var array */
    private $dataTablesColumns = [];

    /** @var array */
    private $dataTablesColumnDefs = [];

    /** @var int */
    private $pageLength = 10;

    /** @var int */
    private $countSavedFilters = 0;

    /** @var Error */
    private $error;

    /** @var string */
    private $menuConfig = '';

    /** @var string */
    private $menuName = '';

    /** @var int */
    private $maxLength = 6000;

    /** @var int */
    private $lastScrollY = null;

    /** @var int */
    private $lastRowIndex = null;

    /** @var array */
    private $displayNames = [];

    private $getRequestParams = null;

    private $isCached = false;

    public function __construct()
    {
        $this->error = new Error();
    }

    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return mixed
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setCachedMode($cacheMode)
    {
        $this->isCached = $cacheMode;
    }

    public function isCached()
    {
        return $this->isCached;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[$filter->getFieldName()] = $filter;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function setDefaultOrder($defaultOrder)
    {
        return $this->defaultOrder = $defaultOrder;
    }

    public function getDefaultOrder()
    {
        return $this->defaultOrder;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getFirstLineData()
    {
        if (count($this->data) > 0) {
            return $this->data[0];
        }

        return [];
    }

    public function setCalcView($calcView)
    {
        return $this->calcView = $calcView;
    }

    /**
     * @return mixed
     */
    public function getCalcView()
    {
        return $this->calcView;
    }

    /**
     * @return mixed
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

    public function getColumnsFieldNames()
    {
        $fieldNames = [];
        /** @var Column $column */
        foreach ($this->columns as $column) {
            $fieldNames[] = $column->getFieldName();
        }

        return $fieldNames;
    }

    /**
     * @param $entityFields
     *
     * @return mixed
     *
     * Return fields who exist in entity and in Json File
     */
    public function getAvailableFields($entityFields)
    {
        $fields = [];

        if (!empty($this->columns)) {
            /** @var Column $column */
            foreach ($this->columns as $column) {
                // Need to get property name to check it in entity
                /** @var Property $property */
                foreach ($entityFields as $property) {
                    if ($property->getField() === $column->getFieldName()) {
                        $fields[$column->getFieldName()] = $property;
                    } elseif ($property->getField() === $column->getIconFieldName()) {
                        $fields[$column->getIconFieldName()] = $property;
                    } elseif ($property->getField() === $column->getLabelFieldName()) {
                        $fields[$column->getLabelFieldName()] = $property;
                    } elseif (null !== $column->getCellAction()) {
                        if (!empty($column->getCellAction()->getParams())) {
                            foreach ($column->getCellAction()->getParams() as $key => $value) {
                                if ($key === $property->getField()) {
                                    $fields[$key] = $property;
                                }
                            }
                        }

                        if ('' != $column->getCellAction()->getRenderFieldName()) {
                            if ($column->getCellAction()->getRenderFieldName() == $property->getField()) {
                                $fields[$column->getCellAction()->getRenderFieldName()] = $property;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($this->filters)) {
            /** @var Filter $filter */
            foreach ($this->filters as $filter) {
                foreach ($entityFields as $property) {
                    if ($property->getField() == $filter->getFieldName()) {
                        $fields[$filter->getFieldName()] = $property;
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getGlobalActions(): ?array
    {
        return $this->globalActions;
    }

    public function getGlobalActionsByType($type): ?array
    {
        $return = [];
        /** @var GlobalAction $globalAction */
        foreach ($this->globalActions as $globalAction) {
            if ($globalAction->getType() == $type) {
                $return[] = $globalAction;
            }
        }

        return $return;
    }

    public function addGlobalAction(GlobalAction $globalAction)
    {
        $this->globalActions[] = $globalAction;
    }

    public function getError(): Error
    {
        return $this->error;
    }

    public function getFilterByFieldName($fieldName)
    {
        /** @var Filter $filter */
        foreach ($this->filters as $filter) {
            if ($filter->getFieldName() == $fieldName) {
                return $filter;
            }
        }

        return false;
    }

    public function getColumnByFieldName($fieldName)
    {
        /** @var Column $column */
        foreach ($this->filters as $column) {
            if ($column->getFieldName() == $fieldName) {
                return $column;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPageLength()
    {
        return $this->pageLength;
    }

    /**
     * @param mixed $pageLength
     */
    public function setPageLength($pageLength): void
    {
        $this->pageLength = $pageLength;
    }

    /**
     * @return mixed
     */
    public function getMenuConfig()
    {
        return $this->menuConfig;
    }

    /**
     * @param $menuConfig
     */
    public function setMenuConfig($menuConfig): void
    {
        $this->menuConfig = $menuConfig;
    }

    public function getSavedFilters(): int
    {
        return $this->countSavedFilters;
    }

    public function addSavedFilters()
    {
        $this->countSavedFilters = $this->countSavedFilters + 1;
    }

    /**
     * @return string
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): void
    {
        $this->maxLength = $maxLength;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function setMenuName(string $menuName): void
    {
        $this->menuName = $menuName;
    }

    public function hasCriticalError()
    {
        if (
            $this->error->hasErrorColumn() || $this->error->hasErrorFilter() || $this->error->isClassExist() || $this->error->isViewExist() || $this->error->isFileIsBroken()) {
            return false;
        }

        return true;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getEntityName()
    {
        if ($this->entity instanceof Entity) {
            return $this->entity->getName();
        }

        return null;
    }

    public function getColumnsWithLinks()
    {
        $columns = [];
        /** @var Column $column */
        foreach ($this->columns as $column) {
            if (null !== $column->getCellAction()) {
                if (in_array($column->getCellAction()->getFunctionName(), [Column::FUNCTION_NAME_DISPLAY_LINK, Column::FUNCTION_NAME_LINK, Column::FUNCTION_NAME_DISPLAY_LINKS])) {
                    $columns[] = $column;
                }
            }
        }

        return $columns;
    }

    public function getSearchProperty()
    {
        if (null !== $this->entity && null !== $this->entity->getProperty(self::FIELD_GLOBAL_SEARCH)) {
            return $this->entity->getProperty(self::FIELD_GLOBAL_SEARCH);
        }

        return null;
    }

    public function getDataTablesColumnDefs(): array
    {
        return $this->dataTablesColumnDefs;
    }

    /**
     * @return Display
     */
    public function setDataTablesColumnDefs(array $dataTablesColumnDefs)
    {
        $this->dataTablesColumnDefs = $dataTablesColumnDefs;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDataTablesColumns(array $dataTablesColumns)
    {
        $this->dataTablesColumns = $dataTablesColumns;

        return $this;
    }

    public function getDataTablesColumns(): array
    {
        return $this->dataTablesColumns;
    }

    public function getUpdateSapColumns(): array
    {
        return $this->getColumnsByType(Column::COL_TYPE_UPDATE_SAP);
    }

    public function getOpenFormColumns(): array
    {
        $columnsAction = $this->getColumnsByType(Column::COL_TYPE_ACTION);
        $columns = [];
        /** @var Column $columnAction */
        foreach ($columnsAction as $columnAction) {
            if (CellAction::FUNCTION_OPEN_FORM === $columnAction->getCellAction()->getFunctionName()) {
                $columns[] = $columnAction;
            }
        }

        return $columns;
    }

    public function getRenderElements()
    {
        $renderElements = [];
        foreach ($this->getColumns() as $column) {
            if (null !== $column->getRenderElement()) {
                $renderElements[] = $column->getRenderElement();
            }
        }

        return $renderElements;
    }

    /**
     * @return int
     */
    public function getLastScrollY(): ?int
    {
        return $this->lastScrollY;
    }

    /**
     * @param int $lastScrollYPos
     */
    public function setLastScrollY(int $lastScrollY): void
    {
        $this->lastScrollY = $lastScrollY;
    }

    /**
     * @return int
     */
    public function getLastRowIndex(): ?int
    {
        return $this->lastRowIndex;
    }

    public function setLastRowIndex(int $lastRowIndex): void
    {
        $this->lastRowIndex = $lastRowIndex;
    }

    /**
     * @param $value
     */
    public function setDisplayNames($value)
    {
        $this->displayNames = $value;
    }

    /**
     * @return array
     */
    public function getDisplayNames()
    {
        return $this->displayNames;
    }

    private function getColumnsByType(string $type)
    {
        $columns = [];
        /** @var Column $column */
        foreach ($this->columns as $column) {
            if ($column->getType() === $type) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    /**
     * @return ParameterBag
     */
    public function getGetRequestParams(): ?ParameterBag
    {
        return $this->getRequestParams;
    }

    /**
     * @param ParameterBag $getRequestParams
     * @return Display
     */
    public function setGetRequestParams(ParameterBag $getRequestParams): Display
    {
        $this->getRequestParams = $getRequestParams;
        return $this;
    }


}
