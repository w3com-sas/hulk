<?php

namespace W3com\HulkBundle\Model;

class SearchView
{
    private $calcView;

    private $columns = [];

    private $filters = [];

    public function __construct($calcView, array $columns, array $filters = [])
    {
        $this->calcView = $calcView;
        $this->columns = $columns;
        $this->filters = $filters;
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

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

}