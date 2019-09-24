<?php

namespace W3com\HulkBundle\Finder;

use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Model\Display;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Service\BoomManager;

class ModelFinder
{

    private $generator;

    public function __construct(BoomGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param Display $dataTable
     * @return Display
     */
    public function setDataTableEntity(Display $dataTable)
    {
        $entities = $this->generator->getAppInspector()->getEntities();
        /** @var Entity $entity */
        foreach ($entities as $entity){
            if ($entity->getTable() === $dataTable->getCalcView()){
                $dataTable->setEntity($entity->getName());
                return $dataTable;
            }
        }
    }

    /**
     * @param Display $dataTable
     * @return array|mixed
     * @throws \ReflectionException
     */
    public function getAvailableProperties(Display $dataTable)
    {
        $entity = $this->generator->getAppInspector()->getEntity($dataTable->getEntity());

        if ($entity === null){
            $dataTable->getError()->setClassExist(false);
            return $dataTable;
        }
        // Return only concerned fields
        return $dataTable->getAvailableFields($entity->getProperties());
    }
}