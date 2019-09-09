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

    public function checkFiltersDefaultValue(Display $dataTable)
    {
        if ($this->session->has('filters')) {

            foreach ($this->session->get('filters') as $filterLocation => $filters) {

                if ($filterLocation === $this->concernedPage) {

                    foreach ($filters as $filterSessionName => $filterSessionValue) {

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
}