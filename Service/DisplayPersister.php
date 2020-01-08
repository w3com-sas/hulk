<?php

namespace W3com\HulkBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\AppInspector;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;

class DisplayPersister
{
    private $boom;

    private $request;

    private $logger;
    /**
     * @var BoomGenerator
     */
    private $generator;

    public function __construct(BoomManager $boom, RequestStack $request, LoggerInterface $logger,
                                BoomGenerator $generator)
    {
        $this->generator = $generator;
        $this->boom = $boom;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function updateSapLine()
    {
        $entityName = $this->request->getCurrentRequest()->request->get('entity');
        $keyValue = $this->request->getCurrentRequest()->request->get('key');
        $targetData = $this->request->getCurrentRequest()->request->get('targetData');
        $targetField = $this->request->getCurrentRequest()->request->get('targetField');
        $entity = $this->generator->getAppInspector()->getEntity($entityName);
        /** @var AbstractEntity $obj */
        $obj = $this->boom->getRepository($entity->getName())->find($keyValue);
        $obj->set($obj->getPropertyByColumn($targetField), $targetData);
        $this->boom->getRepository($entity->getName())->update($obj);
    }

    public function displayUpdate()
    {
        $data = [];
        $data['rows'] = $this->request->getCurrentRequest()->request->get('data');
        $data['targetEntity'] = $this->request->getCurrentRequest()->request->get('targetEntity');
        $data['targetField'] = $this->request->getCurrentRequest()->request->get('targetField');
        $data['entityKey'] = $this->request->getCurrentRequest()->request->get('entityKey');
        $data['targetData'] = $this->request->getCurrentRequest()->request->get('targetData');

        if ($data['rows'] === null) {
            return new JsonResponse(['error' => 'no lines selected'], 422);
        } else {
            foreach ($data['rows'] as $row) {

                $entityKey = null;
                foreach ($row as $field => $value) {

                    if ($field === $data['entityKey']) {
                        $entityKey = $value;
                    }

                    if ($entityKey !== null) {

                        // Get
                        try {
                            $obj = $this->boom->getRepository($data['targetEntity'])->find($entityKey);
                        } catch (EntityNotFoundException $exception) {
                            $this->logger->error('Error when try to get ' . $data['targetEntity'] .
                                ' : ' . $entityKey,
                                $exception->getTrace());
                            return new JsonResponse(['error' => 'Unexistent entity ' . $data['targetEntity']],
                                400);
                        }

                        $property = $obj->getPropertyByColumn($data['targetField']);
                        $dataToSet = $this->formatData($data['targetData']);
                        $obj->set($property, $dataToSet);

                        // Update
                        try {
                            $this->boom->getRepository($data['targetEntity'])->update($obj);
                        } catch (\Exception $e) {
                            $this->logger->error('Failed to update : ' . $e->getMessage(),
                                $e->getTrace());
                        }
                        break;
                    }
                }

                if (!isset($entityKey)) {
                    $this->logger->error('Error : missing ID of ' . $data['targetEntity'] . 'in 
                    the lines of the table.');
                    return new JsonResponse(['error' => 'Missing mandatory ID key to update'], 400);
                }
            }
        }
    }

    private function formatData($targetData)
    {
        if (\DateTime::createFromFormat('d/m/Y', $targetData) !== false) {
            $dateTime = \DateTime::createFromFormat('d/m/Y', $targetData);
            return $dateTime->format('Y-m-d');
        }
        return $targetData;
    }
}