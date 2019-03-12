<?php

namespace W3com\HulkBundle\Model;


class Filter
{
    const FIELD_FIELDNAME = 'FieldName';
    const FIELD_LABEL = 'Label';
    const FIELD_TYPE = 'Type';

    const TYPE_SINGLE = 'single';
    const TYPE_MULTIPLE = 'multiple';
    const TYPE_MULTIPLE_DATE = 'multiple-date';

    private $fieldName;

    private $label;

    private $values;

    private $active;

    private $index;

    private $type;

    private $defaultValue;

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
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $fieldName
     */
    public function setFieldName($fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param mixed $value
     */
    public function addValue($value): void
    {
        $this->values[$value] = $value;
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
        switch ($type) {
            case $this::TYPE_SINGLE:
                $this->type = $type;
                break;
            case $this::TYPE_MULTIPLE;
                $this->type = $type;
                break;
            case $this::TYPE_MULTIPLE_DATE:
                $this->type = $type;
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}