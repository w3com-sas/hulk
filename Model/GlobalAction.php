<?php

namespace W3com\HulkBundle\Model;

class GlobalAction
{
    const FIELD_COLOR = 'Color';
    const FIELD_CONFIG = 'Config';
    const FIELD_FIELDS = 'Fields';
    const FIELD_ICON = 'Icon';
    const FIELD_LABEL = 'Label';
    const FIELD_TYPE = 'Type';
    const TYPE_ANONYME = 'anonyme';
    const TYPE_API_CALL = 'api-call';
    const TYPE_API_REQUEST = 'api-request';
    const TYPE_CALL_FUNCTION = 'call-function';
    const TYPE_CREATE_SAP = 'create-sap';
    const TYPE_EXPORT_CSV = 'export-csv';
    const TYPE_HTML_RENDER = 'html-render';
    const TYPE_PRINT = 'print';
    const TYPE_RENDER_VIEW = 'render-view';
    const TYPE_UPDATE_SAP = 'update-sap';
    const TYPE_LINK = 'link';

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $color;

    /**
     * @var array
     */
    private $fields = [];

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
