<?php

namespace W3com\HulkBundle\Model;

class Config
{
    const DATA_TYPE_ARRAY = 'array';
    const DATA_TYPE_CONTROLLED_LIST = 'controlledList';
    const DATA_TYPE_QUERY = 'query';
    const DATA_TYPE_STATIC = 'static';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_DATE = 'date';
    const FIELD_ENTITY = 'Entity';
    const FIELD_ENTITY_KEY = 'EntityColumnKey';
    const FIELD_MESSAGE_CONFIRM = 'MessageConfirm';
    const FIELD_TARGET_CHOICES = 'TargetChoices';
    const FIELD_TARGET_DATA = 'TargetData';
    const FIELD_TARGET_DATA_TYPE = 'TargetDataType';
    const FIELD_TARGET_DATA_TYPE_ENTITY = 'TargetDataTypeEntity';
    const FIELD_TARGET_DESCRIPTION = 'TargetDescription';
    const FIELD_TARGET_FIELD = 'TargetField';
    const FIELD_LABEL = 'Label';
    const FIELD_NAME = 'Name';
    const FIELD_URL = 'Url';
    const FIELD_USER_CONFIRM = 'UserConfirm';

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $entityKey;

    /**
     * @var string
     */
    private $targetField;

    /**
     * @var string
     */
    private $targetDescription;

    /**
     * @var string
     */
    private $targetDataType;

    /**
     * @var array | string
     */
    private $targetData;

    /**
     * @var string
     */
    private $targetChoices;

    /**
     * @var string
     */
    private $targetDataTypeEntity;

    /**
     * @var bool
     */
    private $userConfirm;

    /**
     * @var string
     */
    private $messageConfirm;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var string
     */
    private $inputType;

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function getEntityKey(): ?string
    {
        return $this->entityKey;
    }

    public function setEntityKey(string $entityKey): void
    {
        $this->entityKey = $entityKey;
    }

    public function getTargetField(): ?string
    {
        return $this->targetField;
    }

    public function setTargetField(string $targetField): void
    {
        $this->targetField = $targetField;
    }

    public function getTargetDataType(): ?string
    {
        return $this->targetDataType;
    }

    public function setTargetDataType(string $targetDataType): void
    {
        $this->targetDataType = $targetDataType;
    }

    public function getTargetData()
    {
        return $this->targetData;
    }

    public function setTargetData($targetData): void
    {
        $this->targetData = $targetData;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getTargetDescription(): ?string
    {
        return $this->targetDescription;
    }

    public function setTargetDescription(string $targetDescription): void
    {
        $this->targetDescription = $targetDescription;
    }

    public function getTargetChoices(): ?string
    {
        return $this->targetChoices;
    }

    public function setTargetChoices(string $targetChoices): void
    {
        if ('' !== $targetChoices) {
            $targetData = [];
            foreach (explode('#', $targetChoices) as $choice) {
                $targetData[explode('|', $choice)[0]] = explode('|', $choice)[1];
            }
            $targetChoices = $targetData;
        }
        $this->targetChoices = $targetChoices;
    }

    public function getTargetDataTypeEntity(): ?string
    {
        return $this->targetDataTypeEntity;
    }

    public function setTargetDataTypeEntity(string $targetDataTypeEntity): void
    {
        $this->targetDataTypeEntity = $targetDataTypeEntity;
    }

    public function getUserConfirm(): ?bool
    {
        return $this->userConfirm;
    }

    public function setUserConfirm(bool $userConfirm): void
    {
        $this->userConfirm = $userConfirm;
    }

    public function getMessageConfirm(): ?string
    {
        return $this->messageConfirm;
    }

    public function setMessageConfirm(string $messageConfirm): void
    {
        $this->messageConfirm = $messageConfirm;
    }

    public function getInputType(): string
    {
        return $this->inputType;
    }

    public function setInputType(string $inputType): void
    {
        $this->inputType = $inputType;
    }
}
