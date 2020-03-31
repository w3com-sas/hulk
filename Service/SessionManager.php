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
        $scrollY = $this->request->getCurrentRequest()->request->get('scrollY');
        $name = $this->request->getCurrentRequest()->request->get('currentRoute');

        if ($this->session->has('yPos')) {
            $oldRows = $this->session->get('yPos');
            $oldRows[$name] = $scrollY;
            $this->session->set('yPos', $oldRows);
        } else {
            $this->session->set('yPos', [$name => $scrollY]);
        }
    }

    /**
     * @param Display $dataTable
     */
    public function setLastRowIndex(Display $dataTable)
    {
        if ($this->session->has('yPos')) {

            foreach ($this->session->get('yPos') as $display => $scrollY) {

                if ($display === $this->concernedPage) {
                    $dataTable->setLastScrollY($scrollY);
                    break;
                }
            }
        }
    }
}