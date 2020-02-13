<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\HulkBundle\Service\DisplayPersister;

class PersistenceController extends AbstractController
{
    private $request;

    private $logger;

    private $displayPersister;

    public function __construct(LoggerInterface $logger, RequestStack $request, DisplayPersister $displayPersister)
    {
        $this->displayPersister = $displayPersister;
        $this->request = $request->getCurrentRequest();
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSapLines()
    {
        $this->checkRequest();
        $lines = $this->request->request->get('lines');
        $entityName = $this->request->request->get('entityName');
        $displayEntityKey = $this->request->request->get('entityKey');
        $affectedField = $this->request->request->get('affectedField');
        $affectedValue = $this->request->request->get('affectedValue');

        try {
            $this->displayPersister->displayUpdate($lines, $entityName, $displayEntityKey, $affectedField, $affectedValue);
        } catch (\Exception $exception){
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return new JsonResponse(['valid' => false, 500]);
        }
        return new JsonResponse(['valid' => true], 200);
    }

    public function updateSapLine()
    {
        $entityName = $this->request->request->get('entity');
        $displayEntityKey = $this->request->request->get('key');
        $affectedField = $this->request->request->get('affectedField');
        $affectedValue = $this->request->request->get('affectedValue');

        try {
            $this->displayPersister->updateSapLine($entityName, $displayEntityKey, $affectedField, $affectedValue);
        } catch (\Exception $exception){
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return new JsonResponse(['valid' => false], 500);
        }
        return new JsonResponse(['valid' => true], 200);
    }

    private function checkRequest()
    {
        if (!$this->request->request->has('lines') ||
            !$this->request->request->has('entityName')||
            !$this->request->request->has('affectedField') ||
            !$this->request->request->has('affectedValue')) {
            $this->logger->error('Bad request');
            return new JsonResponse(['valid' => false], 400);
        }
    }

}