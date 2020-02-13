<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Generator\AppInspector;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomManager;

class DisplayPersister
{
    /** @var BoomManager */
    private $boom;

    /** @var LoggerInterface */
    private $logger;

    /** @var AppInspector */
    private $appInspector;

    public function __construct(BoomManager $boom, LoggerInterface $logger, AppInspector $appInspector)
    {
        $this->appInspector = $appInspector;
        $this->boom = $boom;
        $this->logger = $logger;
    }

    /**
     * @param $entityName
     * @param $key
     * @param $affectedField
     * @param $affectedValue
     * @throws AnnotationException
     * @throws EntityNotFoundException
     * @throws \ReflectionException
     */
    public function updateSapLine($entityName, $key, $affectedField, $affectedValue)
    {
        $entity = $this->getEntity($entityName);
        /** @var AbstractEntity $obj */
        $obj = $this->boom->getRepository($entity->getName())->find($key);
        $obj->set($obj->getPropertyByColumn($affectedField), $affectedValue);
        $this->boom->getRepository($entity->getName())->update($obj);
    }

    /**
     * @param $lines
     * @param $entityName
     * @param $displayEntityKey
     * @param $affectedField
     * @param $affectedValue
     * @throws AnnotationException
     * @throws EntityNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    public function displayUpdate($lines, $entityName, $displayEntityKey, $affectedField, $affectedValue)
    {
        $entity = $this->getEntity($entityName);

        foreach ($lines as $row) {

            if (!array_key_exists($displayEntityKey, $row)) {
                throw new ResourceNotFoundException('Unable to find key ' . $displayEntityKey . ' in display');
            }

            $obj = $this->boom->getRepository($entity->getName())->find($row[$displayEntityKey]);
            $affectedProperty = $obj->getPropertyByColumn($affectedField);

            if ($affectedProperty === "") {
                throw new ResourceNotFoundException('Unable to property ' . $displayEntityKey . ' in display');
            }

            $obj->set($affectedProperty, $this->formatData($affectedValue));
            $this->boom->update($obj);
        }
        $this->boom->flush();
    }

    private function formatData($targetData)
    {
        if (\DateTime::createFromFormat('d/m/Y', $targetData) !== false) {
            $dateTime = \DateTime::createFromFormat('d/m/Y', $targetData);
            return $dateTime->format('Y-m-d');
        }
        return $targetData;
    }

    private function getEntity($entityName)
    {
        $entity = $this->appInspector->getEntity($entityName);
        if ($entity === null) {
            throw new EntityNotFoundException('Unable to find ' . $entity . ' to update sap line.');
        }
        return $entity;
    }
}