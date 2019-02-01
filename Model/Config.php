<?php

namespace W3com\HulkBundle\Model;

class Config
{

    const FIELD_ENTITY = 'Entity';
    const FIELD_ENTITY_KEY = 'EntityColumnKey';
    const FIELD_TARGET_FIELD = 'TargetField';
    const FIELD_TARGET_DATA_TYPE = 'TargetDataType';
    const FIELD_TARGET_DATA = 'TargetData';

    const DATA_TYPE_STATIC = 'static';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_ARRAY = 'array';
    const DATA_TYPE_QUERY = 'query';

    private $entity;

    private $entityKey;

    private $targetField;

    private $targetDataType;

    private $targetData;

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntityKey()
    {
        return $this->entityKey;
    }

    /**
     * @param mixed $entityKey
     */
    public function setEntityKey($entityKey): void
    {
        $this->entityKey = $entityKey;
    }

    /**
     * @return mixed
     */
    public function getTargetField()
    {
        return $this->targetField;
    }

    /**
     * @param mixed $targetField
     */
    public function setTargetField($targetField): void
    {
        $this->targetField = $targetField;
    }

    /**
     * @return mixed
     */
    public function getTargetDataType()
    {
        return $this->targetDataType;
    }

    /**
     * @param mixed $targetDataType
     */
    public function setTargetDataType($targetDataType): void
    {
        $this->targetDataType = $targetDataType;
    }

    /**
     * @return mixed
     */
    public function getTargetData()
    {
        return $this->targetData;
    }

    /**
     * @param mixed $targetData
     */
    public function setTargetData($targetData): void
    {
        $this->targetData = $targetData;
    }
}