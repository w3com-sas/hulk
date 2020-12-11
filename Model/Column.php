<?php

namespace W3com\HulkBundle\Model;

class Column
{
    const FIELD_CELL_ACTION = 'CellAction';
    const FIELD_FIELDNAME = 'FieldName';
    const FIELD_HIDDEN = 'Hidden';
    const FIELD_ICON = 'Icon';
    const FIELD_ICON_FIELDNAME = 'IconFieldName';
    const FIELD_LABEL = 'Label';
    const FIELD_LABEL_FIELDNAME = 'LabelFieldName';
    const FIELD_ORDERABLE = 'Orderable';
    const FIELD_PARAMS = 'Params';
    const FIELD_RENDER = 'Render';
    const FIELD_RENDER_ELEMENT_OPTIONS = 'RenderElementOptions';
    const FIELD_RENDER_FIELDNAME = 'RenderFieldName';
    const FIELD_SEARCHABLE = 'Searchable';
    const FIELD_TYPE = 'Type';
    const FIELD_WIDTH = 'Width';

    const FUNCTION_NAME_DISPLAY_LINK = 'displayLink';
    const FUNCTION_NAME_DISPLAY_LINKS = 'displayLinks';
    const FUNCTION_NAME_LINK = 'link';

    const COL_TYPE_ACTION = 'action';
    const COL_TYPE_CALL_FUNCTION = 'call-function';
    const COL_TYPE_CHECKBOX = 'checkbox';
    const COL_TYPE_ICON = 'icon';
    const COL_TYPE_INPUT_NUMBER = 'input-number';
    const COL_TYPE_INPUT_TEXT = 'input-text';
    const COL_TYPE_RENDER_ELEMENT = 'render-element';
    const COL_TYPE_TEXT = 'text';
    const COL_TYPE_UNIVERSAL = 'universal';
    const COL_TYPE_UPDATE_SAP = 'update-sap';

    /**
     * @var int
     */
    private $index;

    /**
     * @var bool
     */
    private $hidden;

    /**
     * @var array
     */
    private $params;

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
    private $iconFieldName;

    /**
     * @var string
     */
    private $labelFieldName;

    /**
     * @var string
     */
    private $renderFieldName;

    /**
     * @var string
     */
    private $render;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $type;

    /**
     * @var CellAction
     */
    private $cellAction;

    /**
     * @var string
     */
    private $active;

    /**
     * @var int
     */
    private $width;

    /**
     * @var bool
     */
    private $searchable = true;

    /**
     * @var bool
     */
    private $orderable = false;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RenderElement
     */
    private $renderElement;

    /**
     * @var array
     */
    private $renderElementOptions = [];

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setActive(string $active): void
    {
        $this->active = $active;
    }

    public function getActive(): string
    {
        return $this->active;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasCellAction(): bool
    {
        return $this->cellAction instanceof CellAction;
    }

    public function setCellAction(CellAction $cellAction): void
    {
        $this->cellAction = $cellAction;
    }

    public function getCellAction(): ?CellAction
    {
        return $this->cellAction;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getWidth(): ?int
    {
        return $this->width;
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

    public function setIconFieldName(string $iconFieldName): void
    {
        $this->iconFieldName = $iconFieldName;
    }

    public function getIconFieldName(): string
    {
        return $this->iconFieldName;
    }

    public function setLabelFieldName(string $labelFieldName): void
    {
        $this->labelFieldName = $labelFieldName;
    }

    public function getLabelFieldName(): string
    {
        return $this->labelFieldName;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setRenderFieldName(string $renderFieldName): void
    {
        $this->renderFieldName = $renderFieldName;
    }

    public function getRenderFieldName(): ?string
    {
        return $this->renderFieldName;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setRenderElementOptions(array $renderElementOptions): void
    {
        $this->renderElementOptions = $renderElementOptions;
    }

    public function getRenderElementOptions(): array
    {
        return $this->renderElementOptions;
    }

    public function setRenderElement(RenderElement $renderElement): void
    {
        $this->renderElement = $renderElement;
    }

    public function getRenderElement(): ?RenderElement
    {
        return $this->renderElement;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setRender(string $render): void
    {
        $this->render = $render;
    }

    public function getRender(): ?string
    {
        return $this->render;
    }
}
