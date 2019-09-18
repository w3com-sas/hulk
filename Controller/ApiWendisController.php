<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\HulkBundle\Service\ApiManager;

class ApiWendisController extends AbstractController
{

    private $request;

    private $logger;

    private $apiManager;

    public function __construct(RequestStack $request, LoggerInterface $logger)
    {
        $this->request = $request;
        $this->logger = $logger;
        $this->apiManager = new ApiManager($logger);
    }

    /**
     * @return JsonResponse
     */
    public function apiRequest()
    {
        $this->manageRequest();
        $data = $this->request->getCurrentRequest()->request->all();
        $response = $this->apiManager->manageApiCalls($data);
        return new JsonResponse($response, 200);
    }

    private function manageRequest()
    {
        if (!$this->request->getCurrentRequest()->request->has('data') ||
            !$this->request->getCurrentRequest()->request->has('apiParams') ||
            !$this->request->getCurrentRequest()->request->has('urlApi')) {

            $this->logger->error('Missing data to update in the Json file.');
            return new JsonResponse(['valid' => false], 400);
        }
    }

}