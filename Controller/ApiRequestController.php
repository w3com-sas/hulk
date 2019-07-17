<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\HulkBundle\Service\ApiManager;

class ApiRequestController extends AbstractController
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

    public function apiRequest()
    {
        $this->manageRequest();
        $data = $this->request->getCurrentRequest()->request->all();

        $dataApiRequests = [];

        foreach ($data['data'] as $line){

            $dataApiRequest = [];
            foreach ($line as $field => $value){
                if (array_key_exists($field, $data['apiParams']['data'])){
                    $dataApiRequest['data'][$data['apiParams']['data'][$field]] = $value;
                }
            }

            $dataApiRequest['data'] = array_merge($data['apiParams']['data'], $dataApiRequest['data']);
            $dataApiRequests[] = array_merge($data['apiParams'], $dataApiRequest);
        }



        foreach ($dataApiRequests as $dataApiRequest){

            try {
                $this->apiManager->post($dataApiRequest, $data['urlApi']);
            } catch (\Exception $e){
                $this->logger->error($e->getMessage(), $e->getTrace());
            }

        }

        return new JsonResponse('', 200);
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