<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\HulkBundle\Service\DisplayPersister;

class UpdateSapController extends AbstractController
{
    private $request;

    private $logger;

    private $displayPersister;

    public function __construct(LoggerInterface $logger, RequestStack $request, DisplayPersister $displayPersister)
    {
        $this->displayPersister = $displayPersister;
        $this->request = $request;
        $this->logger = $logger;
    }


    /**
     * @throws \Exception
     */
    public function updateSap()
    {
        $this->manageRequest();
        try {
            $this->displayPersister->displayUpdate();
        } catch (EntityNotFoundException $exception){
            return new JsonResponse(['valid' => false, 'error' => 'Entity not found']);
        } catch (\Exception $exception){
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return new JsonResponse(['valid' => false, 'error' => 'Unknown']);
        }
        return new JsonResponse(['valid' => true], 200);
    }

    private function manageRequest()
    {
        if (!$this->request->getCurrentRequest()->request->has('data') ||
            !$this->request->getCurrentRequest()->request->has('targetEntity')||
            !$this->request->getCurrentRequest()->request->has('targetField') ||
            !$this->request->getCurrentRequest()->request->has('entityKey') ||
            !$this->request->getCurrentRequest()->request->has('targetData')) {
            $this->logger->error('Missing data to update in the Json file.');
            return new JsonResponse(['valid' => false], 400);
        }
    }

}