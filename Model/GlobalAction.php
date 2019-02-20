<?php

namespace W3com\HulkBundle\Model;

class GlobalAction
{
    const FIELD_LABEL = 'Label';
    const FIELD_TYPE = 'Type';
    const FIELD_CONFIG = 'Config';

    const TYPE_UPDATE_SAP = 'update-sap';
    const TYPE_CREATE_SAP = 'create-sap';
    const TYPE_EXPORT_CSV = 'export-csv';
    const TYPE_ANONYME = 'anonyme';

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
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
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
    public function getIndex(): string
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex(string $index): void
    {
        $this->index = $index;
    }

}