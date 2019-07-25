<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Service\BoomManager;

class UpdateSapController extends AbstractController
{
    private $boom;

    private $request;

    private $logger;

    public function __construct(BoomManager $boom, RequestStack $request, LoggerInterface $logger)
    {
        $this->boom = $boom;
        $this->request = $request;
        $this->logger = $logger;
    }


    /**
     * @throws \Exception
     */
    public function updateSap()
    {
        $this->manageRequest();
        $data = [];
        $data['rows'] = $this->request->getCurrentRequest()->request->get('data');
        $data['targetEntity'] = $this->request->getCurrentRequest()->request->get('targetEntity');
        $data['targetField'] = $this->request->getCurrentRequest()->request->get('targetField');
        $data['entityKey'] = $this->request->getCurrentRequest()->request->get('entityKey');
        $data['targetData'] = $this->request->getCurrentRequest()->request->get('targetData');

        if ($data['rows'] === null){
            return new JsonResponse(['error' => 'no lines selected'], 422);
        } else {
            foreach ($data['rows'] as $row){

                $entityKey = null;
                foreach ($row as $field => $value){

                    if ($field === $data['entityKey']){
                        $entityKey = $value;
                    }

                    if ($entityKey !== null){

                        // Get
                        try {
                            $obj = $this->boom->getRepository($data['targetEntity'])->find($entityKey);
                        } catch (EntityNotFoundException $exception){
                            $this->logger->error('Error when try to get '.$data['targetEntity'].
                                ' : '.$entityKey,
                                $exception->getTrace());
                            return new JsonResponse(['error' => 'Unexistent entity '.$data['targetEntity']],
                                400);
                        }

                        $property = $obj->getPropertyByColumn($data['targetField']);
                        $dataToSet = $this->formatData($data['targetData']);
                        $obj->set($property, $dataToSet);

                        // Update
                        try {
                            $this->boom->getRepository($data['targetEntity'])->update($obj);
                        } catch (\Exception $e){
                            $this->logger->error('Failed to update : '.$e->getMessage(),
                                $e->getTrace());
                        }
                        break;
                    }
                }
                if (!isset($entityKey)){
                    $this->logger->error('Error : missing ID of '.$data['targetEntity'].'in 
                    the lines of the table.');
                    return new JsonResponse(['error' => 'Missing mandatory ID key to update'], 400);
                }
            }
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

    private function formatData($targetData)
    {
        if (\DateTime::createFromFormat('d/m/Y', $targetData) !== false){
            $dateTime = \DateTime::createFromFormat('d/m/Y', $targetData);
            return $dateTime->format('Y-m-d');
        }
        return $targetData;
    }
}