<?php

namespace W3com\HulkBundle\Finder;

use W3com\HulkBundle\Model\DataTable;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Service\BoomManager;

class ModelFinder
{

    private $boom;

    public function __construct(BoomManager $boom)
    {
        $this->boom = $boom;
    }

    public function checkProjectEntities(DataTable $dataTable)
    {
        $entities = $this->boom->getGenerator()->getAppInspector()->getProjectEntities();

        /** @var Entity $entity */
        foreach ($entities as $entity){
            if ($entity->getTable() === $dataTable->getCalcView()){
                $dataTable->setEntity($entity->getName());
                return $dataTable;
            }
        }
    }

    /**
     * @param $dataTable
     * @return array|mixed
     */
    public function getAvailableProperties(DataTable $dataTable)
    {
        $entity = $this->boom->getGenerator()->getAppInspector()->getProjectEntity($dataTable->getEntity());
        // Return only concerned fields
        return $dataTable->getAvailableFields($entity->getProperties());
    }
}