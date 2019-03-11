<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SaveFiltersController extends AbstractController
{
    private $session;

    private $request;

    public function __construct(SessionInterface $session, RequestStack $request)
    {
        $this->session = $session;
        $this->request = $request;
    }

    public function saveFilters()
    {
        $filters = $this->request->getCurrentRequest()->request->get('filters');
        $name = $this->request->getCurrentRequest()->request->get('currentRoute');


        $formatedFilters = [];

        foreach ($filters as $filter => $value){
            if ($value !== "" && $value !== null){
                $formatedFilters[$filter] = $value;
            }
        }
        $this->session->set($name, $formatedFilters);
        return new Response('');
    }
}