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
    const FIELD_ICON = 'Icon';

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
    private $icon;

    /** @var string */
    private $type;

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

    private $renderElement;

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

    public function getLabel()
    {
        return $this->label;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($index): void
    {
        $this->index = $index;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getCellAction(): ?CellAction
    {
        return $this->cellAction;
    }

    public function hasCellAction(): bool
    {
        return $this->cellAction instanceof CellAction;
    }

    public function setCellAction(CellAction $cellAction): void
    {
        $this->cellAction = $cellAction;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function setSearchable(bool $searchable): void
    {
        $this->searchable = $searchable;
    }

    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    public function setOrderable(bool $orderable): void
    {
        $this->orderable = $orderable;
    }

    public function getIconFieldName()
    {
        return $this->iconFieldName;
    }

    public function setIconFieldName($iconFieldName): void
    {
        $this->iconFieldName = $iconFieldName;
    }

    public function getLabelFieldName()
    {
        return $this->labelFieldName;
    }

    public function setLabelFieldName($labelFieldName): void
    {
        $this->labelFieldName = $labelFieldName;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getRenderFieldName(): ?string
    {
        return $this->renderFieldName;
    }

    public function setRenderFieldName(string $renderFieldName): void
    {
        $this->renderFieldName = $renderFieldName;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getRenderElementOptions(): array
    {
        return $this->renderElementOptions;
    }

    public function setRenderElementOptions(array $renderElementOptions): void
    {
        $this->renderElementOptions = $renderElementOptions;
    }

    public function getRenderElement(): ?RenderElement
    {
        return $this->renderElement;
    }

    public function setRenderElement(RenderElement $renderElement): void
    {
        $this->renderElement = $renderElement;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

}