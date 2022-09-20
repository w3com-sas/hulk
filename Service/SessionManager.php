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
        $this->concernedPage = $this->request->getCurrentRequest()->getRequestUri();
    }

    /**
     * Save last row index clicked in display.
     */
    public function saveRowIndex()
    {
        $scrollY = $this->request->getCurrentRequest()->request->get('scrollY');
        $row = $this->request->getCurrentRequest()->request->get('rowIndex');
        $name = $this->request->getCurrentRequest()->request->get('currentRoute');
        $order = $this->request->getCurrentRequest()->request->get('order');

        if ($this->session->has('rows')) {
            $oldRows = $this->session->get('rows');
            $oldRows[$name] = [
                'scrollY' => $scrollY,
                'index' => $row,
                'order' => $order,
            ];
            $this->session->set('rows', $oldRows);
        } else {
            $this->session->set('rows', [$name => [
                'scrollY' => $scrollY,
                'index' => $row,
                'order' => $order,
            ]]);
        }
    }

    public function setLastRowIndex(Display $dataTable)
    {
        if ($this->session->has('rows')) {
            foreach ($this->session->get('rows') as $display => $row) {
                if ($display === $this->concernedPage) {
                    $dataTable->setLastScrollY($row['scrollY'] ?? 0);
                    $dataTable->setLastRowIndex($row['index'] ?? 0);
                    $dataTable->setOrder($row['order'] ?? []);
                    break;
                }
            }
        }
    }
}
