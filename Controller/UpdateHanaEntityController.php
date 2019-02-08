<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Service\BoomManager;

class UpdateHanaEntityController extends AbstractController
{
    private $boom;

    private $request;

    public function __construct(BoomManager $boom, RequestStack $request)
    {
        $this->boom = $boom;
        $this->request = $request;
    }


    /**
     * @throws \Exception
     */
    public function updateEntity()
    {
        $this->manageRequest();
        $data = [];
        $data['rows'] = $this->request->getCurrentRequest()->request->get('data');
        $data['targetEntity'] = $this->request->getCurrentRequest()->request->get('targetEntity');
        $data['targetField'] = $this->request->getCurrentRequest()->request->get('targetField');
        $data['entityKey'] = $this->request->getCurrentRequest()->request->get('entityKey');
        $data['targetData'] = $this->request->getCurrentRequest()->request->get('targetData');

        if ($data['rows'] === null){
            return new JsonResponse(['error' => 'missing data'], 422);
        } else {
            foreach ($data['rows'] as $row){
                foreach ($row as $field => $value){
                    if ($field === $data['entityKey']){
                        $entityKey = $value;
                    }
                    if (isset($entityKey)){

                        try {
                            $obj = $this->boom->getRepository($data['targetEntity'])->find($entityKey);
                        } catch (EntityNotFoundException $exception){
                            return new JsonResponse(['error' => 'Unexistent entity '.$data['targetEntity']],
                                400);
                        }

                        $obj->set($data['targetField'], $data['targetData']);
                        $this->boom->getRepository($data['targetEntity'])->update($obj);
                        break;
                    }
                }
                if (!isset($entityKey)){
                    return new JsonResponse(['error' => 'Missing mandatory ID key to update'], 400);
                }
            }
        }
        return new JsonResponse(['valid' => true], 200);
    }

    private function manageRequest()
    {
        if (!$this->request->getCurrentRequest()->request->has('data') ||
            !$this->request->getCurrentRequest()->request->get('targetEntity')||
            !$this->request->getCurrentRequest()->request->get('targetField') ||
            !$this->request->getCurrentRequest()->request->get('entityKey') ||
            !$this->request->getCurrentRequest()->request->get('targetData')) {
            return new JsonResponse(['valid' => false], 400);
        }
    }
}