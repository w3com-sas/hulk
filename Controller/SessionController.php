<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use W3com\HulkBundle\Filter\FilterSessionManager;
use W3com\HulkBundle\Service\SessionManager;

class SessionController extends AbstractController
{
    /**
     * @var FilterSessionManager
     */
    private $filterSession;

    /**
     * @var SessionManager
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FilterSessionManager $filterSession, SessionManager $sessionManager, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->filterSession = $filterSession;
        $this->session = $sessionManager;
    }

    public function saveFilter()
    {
        try {
            $countFilters = $this->filterSession->addFilter();
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
            return new JsonResponse(['success' => false]);
        }
        return new JsonResponse(['success' => true, 'countFilters' => $countFilters]);
    }

    public function  saveRowIndex()
    {
        try {
            $this->session->saveRowIndex();
        } catch (\Exception $e){
            $this->logger->error($e->getMessage(), $e->getTrace());
            return new JsonResponse(['success' => false]);
        }
        return new JsonResponse(['success' => true]);
    }

}