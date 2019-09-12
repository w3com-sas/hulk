<?php

namespace W3com\HulkBundle\Model;


use W3com\BoomBundle\Generator\Model\Property;

class Display
{
    const FIELD_CALCVIEW = 'CalculationView';
    const FIELD_GLOBAL_ACTION = 'GlobalActions';
    const FIELD_FILTERS = 'Filters';
    const FIELD_COLUMNS = 'Columns';
    const FIELD_PAGE_LENGTH = 'PageLength';
    const FIELD_DISPLAY_NAME = 'DisplayName';
    const FIELD_MENU_CONFIG = 'MenuConfig';
    const FIELD_MENU_NAME = 'MenuName';

    public function __construct()
    {
        $this->error = new Error();
    }

    public $isFilter = false;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $calcView;

    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var array
     */
    private $globalActions;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var integer
     */
    private $pageLength;

    /**
     * @var integer
     */
    private $countSavedFilters = 0;

    /**
     * @var Error
     */
    private $error;

    /**
     * @var string
     */
    private $menuConfig = '';

    /**
     * @var string
     */
    private $menuName = '';

    /**
     * @var int
     */
    private $maxLength = 6000;

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
        $this->filters[] = $filter;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
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
    public function getEntity()
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


    /**
     * @param $entityFields
     * @return mixed
     *
     * Return fields who exist in entity and in Json File
     */
    public function getAvailableFields($entityFields)
    {
        $fields = [];

        if (!empty($this->columns)){
            /** @var Column $column */
            foreach ($this->columns as $column) {

                // Need to get property name to check it in entity
                /** @var Property $property */
                foreach ($entityFields as $property) {
                    if ($property->getField() === $column->getFieldName()) {
                        $fields[$column->getFieldName()] = $property;
                    } elseif($property->getField() === $column->getIconFieldName()) {
                        $fields[$column->getIconFieldName()] = $property;
                    } elseif($property->getField() === $column->getLabelFieldName()){
                        $fields[$column->getLabelFieldName()] = $property;
                    } elseif ($column->getCellAction() !== null){

                        if (!empty($column->getCellAction()->getParams())){
                            foreach ($column->getCellAction()->getParams() as $key => $value){
                                if ($key === $property->getField()){
                                    $fields[$key] = $property;
                                }
                            }
                        }

                        if($column->getCellAction()->getRenderFieldName() != ''){
                            if($column->getCellAction()->getRenderFieldName() == $property->getField()){
                                $fields[$column->getCellAction()->getRenderFieldName()] = $property;
                            }
                        }
                    }
                }
            }
        }


        if (!empty($this->filters)){
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
        foreach ($this->filters as $filter){

            if ($filter->getFieldName() == $fieldName){
                return $filter;
            }
        }
        return false;
    }

    public function getColumnByFieldName($fieldName)
    {
        /** @var Column $column */
        foreach ($this->filters as $column){

            if ($column->getFieldName() == $fieldName){
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
    public function getFilename(): string
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

}