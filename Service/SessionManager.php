<?php

namespace W3com\HulkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionManager
{
    private $request;


    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }


    public function initSession($fileName)
    {
        if (!$this->request->getCurrentRequest()->hasSession()) {
            $session = new Session();
            $session->start();
        } else {
            $session = $this->request->getCurrentRequest()->getSession();
        }

        $session->remove('table');
        $session->remove('display');

        if (!$session->has('display')) {

            $session->set('display', [
                $fileName => [
                    'filter' => [],
                    'sort' => [],
                ]]);

        } else {
            $data = $session->get('display');

        }

        return $session;
    }

    public function addStructureInfo()
    {
        $session = $this->request->getCurrentRequest()->getSession();
        $data = $session->get('display');
        $data['properties'] = [

        ];
    }


}