<?php

namespace W3com\HulkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use W3com\HulkBundle\Model\Display;

class SessionManager
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
     * Save last row index clicked in display
     */
    public function saveRowIndex()
    {
        $row = $this->request->getCurrentRequest()->request->get('rowIndex');
        $name = $this->request->getCurrentRequest()->request->get('currentRoute');

        if ($this->session->has('rows')) {
            $oldRows = $this->session->get('rows');
            $oldRows[$name] = $row;
            $this->session->set('rows', $oldRows);
        } else {
            $this->session->set('rows', [$name => $row]);
        }
    }

    /**
     * @param Display $dataTable
     */
    public function setLastRowIndex(Display $dataTable)
    {
        if ($this->session->has('rows')) {

            foreach ($this->session->get('rows') as $display => $index) {

                if ($display === $this->concernedPage) {
                    $dataTable->setLastRowIndex($index);
                    break;
                }
            }
        }
    }
}