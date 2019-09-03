<?php

namespace W3com\HulkBundle\Model;

class Column
{
    const FIELD_LABEL = 'Label';
    const FIELD_FIELDNAME = 'FieldName';
    const FIELD_TYPE = 'Type';
    const FIELD_CELL_ACTION = 'CellAction';
    const FIELD_WIDTH = 'Width';
    const FIELD_HIDDEN = 'Hidden';
    const FIELD_ORDERABLE = 'Orderable';

    const COL_TYPE_CHECKBOX = 'checkbox';
    const TYPE_TEXT = 'text';

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
     */
    private $active;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var bool
     */
    private $orderable = false;


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

    public function hasCellAction(): bool
    {
        return $this->cellAction instanceof CellAction;
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

    /**
     * @return int
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return bool
     */
    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    /**
     * @param bool $orderable
     */
    public function setOrderable(bool $orderable): void
    {
        $this->orderable = $orderable;
    }
}