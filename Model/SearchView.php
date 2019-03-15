<?php

namespace W3com\HulkBundle\Model;

class SearchView
{
    private $calcView;

    private $columns = [];

    public function __construct($calcView, array $columns)
    {
        $this->calcView = $calcView;
        $this->columns = $columns;
    }

    /**
     * @return mixed
     */
    public function getCalcView()
    {
        return $this->calcView;
    }

    /**
     * @param mixed $calcView
     */
    public function setCalcView($calcView): void
    {
        $this->calcView = $calcView;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

}