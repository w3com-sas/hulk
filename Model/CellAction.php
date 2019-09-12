<?php

namespace W3com\HulkBundle\Model;

class CellAction
{
    const FIELD_LABEL = 'Label';
    const FIELD_FUNCTION_NAME = 'FunctionName';
    const FIELD_TARGET_ENTITY = 'TargetEntity';
    const FIELD_ICON = 'Icon';
    const FIELD_PARAMS = 'Params';
    const FIELD_RENDER_TYPE = 'RenderType';
    const FIELD_RENDER_VALUE = 'RenderValue';
    const FIELD_RENDER_FIELDNAME = 'RenderFieldName';

    const FUNCTION_DISPLAY_LINK = 'displayLink';
    const FUNCTION_LINK = 'link';
    const FUNCTION_CALL_FUNCTION = 'call-function';

    const FIELD_ICON_FIELDNAME = 'IconFieldName';

    private $label;

    private $functionName;

    private $targetEntity;

    private $icon;

    private $iconFieldName;

    private $params;

    private $iconColumnIndex;

    private $renderType;

    private $renderValue;

    private $renderFieldName;

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

    public function setIconFieldName($value)
    {
        $this->iconFieldName = $value;
    }

    /**
     * @return mixed
     */
    public function getIconFieldName()
    {
        return $this->iconFieldName;
    }

    /**
     * @return mixed
     */
    public function getIconColumnIndex()
    {
        return $this->iconColumnIndex;
    }

    /**
     * @param mixed $iconColumnIndex
     */
    public function setIconColumnIndex($iconColumnIndex): void
    {
        $this->iconColumnIndex = $iconColumnIndex;
    }

    /**
     * @return mixed
     */
    public function getRenderType()
    {
        return $this->renderType;
    }

    /**
     * @param mixed $renderType
     */
    public function setRenderType($renderType): void
    {
        $this->renderType = $renderType;
    }

    /**
     * @return mixed
     */
    public function getRenderValue()
    {
        return $this->renderValue;
    }

    /**
     * @param mixed $renderValue
     */
    public function setRenderValue($renderValue): void
    {
        $this->renderValue = $renderValue;
    }

    /**
     * @return mixed
     */
    public function getRenderFieldName()
    {
        return $this->renderFieldName;
    }

    /**
     * @param mixed $renderFieldName
     */
    public function setRenderFieldName($renderFieldName): void
    {
        $this->renderFieldName = $renderFieldName;
    }
}