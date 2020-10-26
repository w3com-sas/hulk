<?php

namespace W3com\HulkBundle\Model;

class RenderElement
{
    private $name = '';

    private $elementId = '';

    private $elementAttr = [];

    public function __construct(Column $column, $name = '', $elementAttr = [])
    {
        $this->elementId = 'template'.$column->getFieldName();
        $this->name = $name;
        $this->elementAttr = $elementAttr;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getElementId(): string
    {
        return $this->elementId;
    }

    public function setElementId(string $elementId): self
    {
        $this->elementId = $elementId;

        return $this;
    }

    public function getElementAttr(): array
    {
        return $this->elementAttr;
    }

    public function setElementAttr(array $elementAttr): self
    {
        $this->elementAttr = $elementAttr;

        return $this;
    }
}
