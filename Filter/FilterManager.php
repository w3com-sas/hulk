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

    /**
     * FilterManager constructor.
     *
     * For some odd reasons, the services.xml don't put those two needed class as construct argument.
     * So we need to instantiate with a new.
     */
    public function __construct(
        SingleFilterManager $singleFilterManager,
        MultipleFilterManager $multilpleFilterManager
    )
    {
        $this->singleFilterManager = $singleFilterManager;
        $this->multilpleFilterManager = $multilpleFilterManager;
    }

    public function initFilters(Display $dataTable): Display
    {
        $this->singleFilterManager->manageSingleFilters($dataTable);
        $this->multilpleFilterManager->manageMultipleFilters($dataTable);

        return $dataTable;
    }
}
