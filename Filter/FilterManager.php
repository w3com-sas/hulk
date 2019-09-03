<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Display;

class FilterManager extends AbstractFilterManager
{

    private $singleFilterManager;

    private $multilpleFilterManager;

    public function __construct()
    {
        $this->singleFilterManager = new SingleFilterManager();
        $this->multilpleFilterManager = new MultipleFilterManager();
    }

    public function initFilters(Display $dataTable)
    {
        $this->singleFilterManager->manageSingleFilters($dataTable);
        $this->multilpleFilterManager->manageMultipleFilters($dataTable);
        return $dataTable;
    }


}