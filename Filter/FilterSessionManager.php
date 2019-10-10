<?php

namespace W3com\HulkBundle\Filter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

class FilterSessionManager
{

    private $session;

    private $request;

    private $concernedPage;

    public function __construct(SessionInterface $session, RequestStack $request)
    {
        $this->session = $session;
        $this->request = $request;
        $this->concernedPage = $this->request->getCurrentRequest()->get('_route') .
            $this->request->getCurrentRequest()->get('filename');

    }

    /**
     * Return number of filters on the current page
     * @return int
     */
    public function addFilter()
    {
        $filters = $this->request->getCurrentRequest()->request->get('filters');
        $name = $this->request->getCurrentRequest()->request->get('currentRoute');

        $sessionFilters = [];
        $formatedFilters = [];

        foreach ($filters as $filter => $value) {

            // Single filter
            if ($value !== "" && $value !== null) {

                // Multiple filter
                if (is_array($value) && ($value['min'] != "" || $value['max'] != "")) {
                    $formatedFilters[$filter] = $value;

                    // Single
                } elseif (!is_array($value) && $value !== "" && $value !== null) {
                    $formatedFilters[$filter] = $value;
                }
            }
        }
        $sessionFilters[$name] = $formatedFilters;

        if ($this->session->has('filters')) {
            $oldFilters = $this->session->get('filters');
            $sessionFilters = array_merge($oldFilters, $sessionFilters);
        }
        $this->session->set('filters', $sessionFilters);
        return count($sessionFilters[$name]);
    }

    /**
     * @param Display $dataTable
     */
    public function checkFiltersDefaultValue(Display $dataTable)
    {
        if ($this->session->has('filters')) {

            foreach ($this->session->get('filters') as $filterLocation => $filters) {

                if ($filterLocation === $this->concernedPage) {

                    foreach ($filters as $filterSessionName => $filterSessionValue) {

                        if ($filterSessionName === 'searchBar'){
                            $this->addSearchBarFilter($dataTable, $filterSessionValue);
                        }

                        /** @var Filter $filter */
                        foreach ($dataTable->getFilters() as $filter) {

                            if ($filter->getFieldName() === $filterSessionName) {
                                $dataTable->addSavedFilters();
                                $filter->setDefaultValue($filterSessionValue);
                            }
                        }
                    }
                }
            }
        }
    }

    private function addSearchBarFilter(Display $dataTable, $filterSessionValue)
    {
        $filter = new Filter();
        $filter->setDefaultValue($filterSessionValue);
        $filter->setFieldName('searchBar');
        $dataTable->addFilter($filter);
    }
}