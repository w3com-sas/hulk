<?php

namespace W3com\HulkBundle\Model;

class CellAction
{
    const FIELD_LABEL = 'Label';
    const FIELD_FUNCTION_NAME = 'FunctionName';
    const FIELD_TARGET_ENTITY = 'TargetEntity';
    const FIELD_ICON = 'Icon';
    const FIELD_PARAMS = 'Params';

    const FUNCTION_DISPLAY_LINK = 'displayLink';
    private $label;

    private $functionName;

    private $targetEntity;

    private $icon;

    private $params;

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
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * @param mixed $targetEntity
     */
    public function setTargetEntity($targetEntity): void
    {
        $this->targetEntity = $targetEntity;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * @param mixed $functionName
     */
    public function setFunctionName($functionName): void
    {
        $this->functionName = $functionName;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addParam($key, $value): void
    {
        $this->params[$key] = $value;
    }

}