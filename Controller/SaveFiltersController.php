<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SaveFiltersController extends AbstractController
{
    private $session;

    private $request;

    private $logger;

    public function __construct(SessionInterface $session, RequestStack $request, LoggerInterface $logger)
    {
        $this->session = $session;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function saveFilters()
    {

        $filters = $this->request->getCurrentRequest()->request->get('filters');
        $name = $this->request->getCurrentRequest()->request->get('currentRoute');


        $sessionFilters = [];
        $formatedFilters = [];

        foreach ($filters as $filter => $value){

            // Single filter
            if ($value !== "" && $value !== null){

                // Multiple filter
                if (is_array($value) && $value['min'] !== "" && $value['min'] !== null){
                    $formatedFilters[$filter] = $value;

                    // Single
                } elseif (is_array($value) === false && $value !== "" && $value !== null) {
                    $formatedFilters[$filter] = $value;
                }
            }
        }
        $sessionFilters[$name] = $formatedFilters;

        if ($this->session->has('filters')){
            $oldFilters = $this->session->get('filters');
            $sessionFilters = array_merge($oldFilters, $sessionFilters);
        }

        try {
            $this->session->set('filters', $sessionFilters);
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
            return new JsonResponse('Unknow error when trying to save filter in session.', 500);
        }

        return new JsonResponse(['Success', 'countFilters' => count($sessionFilters[$name])]);
    }
}