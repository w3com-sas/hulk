<?php

namespace W3com\HulkBundle\Model;

class Column
{
    const FIELD_LABEL = 'Label';
    const FIELD_FIELDNAME = 'FieldName';
    const FIELD_ICON_FIELDNAME = 'IconFieldName';
    const FIELD_LABEL_FIELDNAME = 'LabelFieldName';
    const FIELD_RENDER_FIELDNAME = 'RenderFieldName';
    const FIELD_TYPE = 'Type';
    const FIELD_CELL_ACTION = 'CellAction';
    const FIELD_WIDTH = 'Width';
    const FIELD_HIDDEN = 'Hidden';
    const FIELD_ORDERABLE = 'Orderable';
    const FIELD_SEARCHABLE = 'Searchable';
    const FIELD_PARAMS = 'Params';
    const FIELD_RENDER_ELEMENT_OPTIONS = 'RenderElementOptions';

    const FUNCTION_NAME_DISPLAY_LINK = 'displayLink';
    const FUNCTION_NAME_LINK = 'link';

    const COL_TYPE_TEXT = 'text';
    const COL_TYPE_CHECKBOX = 'checkbox';
    const COL_TYPE_ICON = 'icon';
    const COL_TYPE_UPDATE_SAP = 'update-sap';
    const COL_TYPE_ACTION = 'action';
    const COL_TYPE_UNIVERSAL = 'universal';
    const COL_TYPE_INPUT_TEXT = 'input-text';
    const COL_TYPE_INPUT_NUMBER = 'input-number';
    const COL_TYPE_RENDER_ELEMENT = 'render-element';

    /** @var integer */
    private $index;

    /** @var bool */
    private $hidden;

    /** @var array */
    private $params;

    /** @var string */
    private $label;

    /** @var string */
    private $fieldName;

    /** @var string */
    private $iconFieldName;

    /** @var string */
    private $labelFieldName;

    /** @var string */
    private $renderFieldName;

    /** @var string */
    private $type;

    /** @var array */
    private $icons;

    /** @var CellAction */
    private $cellAction;

    /** @var string */
    private $active;

    /** @var integer */
    private $width;

    /** @var bool */
    private $searchable = true;

    /** @var bool */
    private $orderable = false;

    /** @var Config */
    private $config;

    /** @var array */
    private $renderElements = [];

    private $renderElement;

    /** @var array */
    private $renderElementOptions = [];

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
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     */
    public function setSearchable(bool $searchable): void
    {
        $this->searchable = $searchable;
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

    /**
     * @return string
     */
    public function getIconFieldName()
    {
        return $this->iconFieldName;
    }

    /**
     * @param string $iconFieldName
     */
    public function setIconFieldName($iconFieldName): void
    {
        $this->iconFieldName = $iconFieldName;
    }

    /**
     * @return string
     */
    public function getLabelFieldName()
    {
        return $this->labelFieldName;
    }

    /**
     * @param string $labelFieldName
     */
    public function setLabelFieldName($labelFieldName): void
    {
        $this->labelFieldName = $labelFieldName;
    }

    /**
     * @return Config
     */
    public function getConfig(): ?Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getRenderFieldName(): ?string
    {
        return $this->renderFieldName;
    }

    /**
     * @param string $renderFieldName
     */
    public function setRenderFieldName(string $renderFieldName): void
    {
        $this->renderFieldName = $renderFieldName;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getRenderElementOptions(): array
    {
        return $this->renderElementOptions;
    }

    /**
     * @param array $renderElementOptions
     */
    public function setRenderElementOptions(array $renderElementOptions): void
    {
        $this->renderElementOptions = $renderElementOptions;
    }

    public function getRenderElement()
    {
        return $this->renderElement;
    }

    public function setRenderElement($renderElement): void
    {
        $this->renderElement = $renderElement;
    }


}