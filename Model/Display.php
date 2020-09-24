<?php

namespace W3com\HulkBundle\Model;


use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Form\DisplayType;
use W3com\HulkBundle\Url\UrlManager;
use W3com\HulkBundle\Util\DisplayConstructor;

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

    /** @var array  */
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

    /** @var integer */
    private $pageLength = 10;

    /** @var integer */
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

    public function __construct()
    {
        $this->error = new Error();
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @param array $columns
     */
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

    /**
     * @param Filter $filter
     */
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

    /**
     * @return array
     */
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
                    } elseif ($column->getCellAction() !== null) {

                        if (!empty($column->getCellAction()->getParams())) {
                            foreach ($column->getCellAction()->getParams() as $key => $value) {
                                if ($key === $property->getField()) {
                                    $fields[$key] = $property;
                                }
                            }
                        }

                        if ($column->getCellAction()->getRenderFieldName() != '') {
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
        foreach($this->globalActions as $globalAction){
            if($globalAction->getType() == $type){
                $return[] = $globalAction;
            }
        }
        return $return;
    }



    /**
     * @param GlobalAction $globalAction
     */
    public function addGlobalAction(GlobalAction $globalAction)
    {
        $this->globalActions[] = $globalAction;
    }

    /**
     * @return Error
     */
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

    /**
     * @return int
     */
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

    /**
     * @param string $displayName
     */
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

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return int
     */
    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * @param int $maxLength
     */
    public function setMaxLength(int $maxLength): void
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return string
     */
    public function getMenuName(): string
    {
        return $this->menuName;
    }

    /**
     * @param string $menuName
     */
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

    /**
     * @param string $label
     */
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
            if ($column->getCellAction() !== null) {
                if (in_array($column->getCellAction()->getFunctionName(), [Column::FUNCTION_NAME_DISPLAY_LINK, Column::FUNCTION_NAME_LINK, Column::FUNCTION_NAME_DISPLAY_LINKS])) {
                    $columns[] = $column;
                }
            }
        }
        return $columns;
    }

    public function getSearchProperty()
    {
        if ($this->entity !== null && $this->entity->getProperty(self::FIELD_GLOBAL_SEARCH) !== null) {
            return $this->entity->getProperty(self::FIELD_GLOBAL_SEARCH);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getDataTablesColumnDefs(): array
    {
        return $this->dataTablesColumnDefs;
    }

    /**
     * @param array $dataTablesColumnDefs
     * @return Display
     */
    public function setDataTablesColumnDefs(array $dataTablesColumnDefs)
    {
        $this->dataTablesColumnDefs = $dataTablesColumnDefs;
        return $this;
    }

    /**
     * @param array $dataTablesColumns
     * @return $this
     */
    public function setDataTablesColumns(array $dataTablesColumns)
    {
        $this->dataTablesColumns = $dataTablesColumns;
        return $this;
    }

    /**
     * @return array
     */
    public function getDataTablesColumns(): array
    {
        return $this->dataTablesColumns;
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
            if ($columnAction->getCellAction()->getFunctionName() === CellAction::FUNCTION_OPEN_FORM) {
                $columns[] = $columnAction;
            }
        }
        return $columns;
    }

    public function getRenderElements()
    {
        $renderElements = [];
        foreach ($this->getColumns() as $column) {
            if ($column->getRenderElement() !== null){
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

    /**
     * @param int $lastRowIndex
     */
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

}
