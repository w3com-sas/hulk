<?php

namespace W3com\HulkBundle\Model;


class Filter
{
    private $field;

    private $label;

    private $order;

    private $values;

    private $active;

    private $index;

    /** @var bool */
    private $columnExist;

    private $property;

    public function __construct($jsonFilter)
    {
        foreach ($jsonFilter as $field => $value) {
            switch ($field) {
                case 'FieldName':
                    $this->setField($value);
                    break;
                case 'Label':
                    $this->setLabel($value);
                    break;
                case 'OrderBy':
                    $this->setOrder($value);
                    break;
            }
        }
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
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field): void
    {
        $this->field = $field;
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
     * @return bool
     */
    public function isColumnExist(): ?bool
    {
        return $this->columnExist;
    }

    /**
     * @param bool $columnExist
     */
    public function setColumnExist(bool $columnExist): void
    {
        $this->columnExist = $columnExist;
    }

    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param mixed $property
     */
    public function setProperty($property): void
    {
        $this->property = $property;
    }



}