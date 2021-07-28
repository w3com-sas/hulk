<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\HulkBundle\Service\ApiManager;

class ApiWendisController extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ApiManager
     */
    private $apiManager;

    public function __construct(RequestStack $request, LoggerInterface $logger, ApiManager $apiManager)
    {
        $this->request = $request;
        $this->logger = $logger;
        $this->apiManager = $apiManager;
    }

    public function apiRequest(): JsonResponse
    {
        $this->manageRequest();
        $data = $this->request->getCurrentRequest()->request->all();
        $response = $this->apiManager->manageApiCalls($data);

        return new JsonResponse($response, 200);
    }

    private function manageRequest(): JsonResponse
    {
        $jsonResponse = new JsonResponse(['valid' => true]);

        if (
            !$this->request->getCurrentRequest()->request->has('data')
            || !$this->request->getCurrentRequest()->request->has('apiParams')
            || !$this->request->getCurrentRequest()->request->has('urlApi')
        ) {
            $this->logger->error('Missing data to update in the Json file.');

            $jsonResponse = new JsonResponse(['valid' => false], 400);
        }

        return $jsonResponse;
    }
}
