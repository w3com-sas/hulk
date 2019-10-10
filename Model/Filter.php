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
    const TYPE_PRE_FILTER = 'pre-filter';
    const FIELD_PARAMS = 'Params';
    const TYPE_DATE = 'date';
    const FIELD_ORDER = 'Order';

    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    private $fieldName;

    private $label;

    private $values;

    private $active;

    private $order;

    private $index;

    private $type;

    private $defaultValue;

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
        if ($this->order === self::ORDER_ASC){
            ksort($this->values, SORT_NATURAL | SORT_FLAG_CASE);
        } elseif ($this->order === self::ORDER_DESC){
            krsort($this->values, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return $this->values;
    }

    /**
     * @param mixed $value
     */
    public function addValue($value): void
    {
        if ($value !== null){
            $this->values[$value] = $value;
        }
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

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

    /**
     * @param $values
     */
    public function setValues(?array $values): void
    {
        $this->values = $values;
    }
}