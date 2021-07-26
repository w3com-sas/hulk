<?php

namespace W3com\HulkBundle\Filter;

use W3com\HulkBundle\Model\Display;

class FilterManager extends AbstractFilterManager
{
    /**
     * @var SingleFilterManager
     */
    private $singleFilterManager;

    /**
     * @var MultipleFilterManager
     */
    private $multilpleFilterManager;

    public function __construct(SingleFilterManager $singleFilterManager, MultipleFilterManager $multilpleFilterManager)
    {
        $this->singleFilterManager = $singleFilterManager;
        $this->multilpleFilterManager = $multilpleFilterManager;
    }

    public function initFilters(Display $dataTable)
    {
        $this->singleFilterManager->manageSingleFilters($dataTable);
        $this->multilpleFilterManager->manageMultipleFilters($dataTable);

        return $dataTable;
    }
}
