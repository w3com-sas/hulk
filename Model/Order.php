<?php

namespace W3com\HulkBundle\Model;

class Order
{
    private $column;

    private $sens;

    public function __construct($order)
    {
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param mixed $column
     */
    public function setColumn($column): void
    {
        $this->column = $column;
    }

    /**
     * @return mixed
     */
    public function getSens()
    {
        return $this->sens;
    }

    /**
     * @param mixed $sens
     */
    public function setSens($sens): void
    {
        $this->sens = $sens;
    }
}