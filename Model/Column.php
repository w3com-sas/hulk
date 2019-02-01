<?php

namespace W3com\HulkBundle\Model;

class Column
{
    const FIELD_LABEL = 'Label';
    const FIELD_FIELDNAME = 'FieldName';
    const FIELD_TYPE = 'Type';
    const FIELD_CELL_ACTION = 'CellAction';

    /**
     * @var integer
     */
    private $index;

    /**
     * @var bool
     */
    private $hidden;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $icons;

    /**
     * @var CellAction
     */
    private $cellAction;

    /**
     * @var string
     * Determine if corresponding field exist in SAP ODATA and prevent error
     */
    private $active;


    public function setLabel($label)
    {
        return $this->label = $label;
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }


    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setActive(string $active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }


    /**
     * @param mixed $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return CellAction
     */
    public function getCellAction(): ?CellAction
    {
        return $this->cellAction;
    }

    /**
     * @param CellAction $cellAction
     */
    public function setCellAction(CellAction $cellAction): void
    {
        $this->cellAction = $cellAction;
    }

    /**
     * @return array
     */
    public function getIcons(): array
    {
        return $this->icons;
    }

    /**
     * @param array $icons
     */
    public function setIcons(array $icons): void
    {
        $this->icons = $icons;
    }
}