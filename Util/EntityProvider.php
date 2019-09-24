<?php

namespace W3com\HulkBundle\Util;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use W3com\BoomBundle\Generator\Model\Entity;
use W3com\BoomBundle\Service\BoomGenerator;

class EntityProvider
{
    private $generator;

    private $cache;

    public function __construct(BoomGenerator $generator, AdapterInterface $cache)
    {
        $this->generator = $generator;
        $this->cache = $cache;
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \ReflectionException
     */
    public function getEntities()
    {
        $this->cache->deleteItem('ods.metadata');
        $this->generator->getAppInspector()->initEntities();
        $entities = $this->generator->getAppInspector()->getEntities();
        $cvEntities = $this->generator->getOdsInspector()->getEntities();
        $this->generator->getSLInspector()->initEntities();
        $udtEntities = $this->generator->getSLInspector()->getEntities();

        $toCreateEntities = [];
        $toUpdateEntities = [];
        $upToDateEntities = [];

        /** @var Entity $onlineEntity */
        foreach (array_merge($cvEntities, $udtEntities) as $onlineEntity){
            /** @var Entity $entity */
            foreach ($entities as $entity){
                if ($entity->getTable() === $onlineEntity->getTable()){
                    if (count($entity->getProperties()) !== count($onlineEntity->getProperties())){
                        $toUpdateEntities[$entity->getTable()] = $onlineEntity;
                    } else {
                        $upToDateEntities[$entity->getTable()] = $onlineEntity;
                    }
                }
            }
            if (!isset($toUpdateEntities[$onlineEntity->getTable()]) && !isset($upToDateEntities[$onlineEntity->getTable()])){
                $toCreateEntities[$onlineEntity->getTable()] = $onlineEntity;
            }

        }
        return ['toUpdate' => $toUpdateEntities, 'toCreate' => $toCreateEntities, 'upToDate' => $upToDateEntities];
    }
}