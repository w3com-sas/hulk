<?php

namespace W3com\HulkBundle\Model;

class Column
{

    private $viewName;

    private $fieldName;

    private $hidden;

    private $index;


    public function __construct($column = [])
    {
        foreach ($column as $field => $value) {

            switch ($field) {

                case 'ColumnName':
                    $this->setViewName($value);
                    break;

                case 'FieldName':
                    $this->setFieldName($value);
                    break;
            }
        }
    }



    /**
     * @var string
     * Determine if corresponding field exist in SAP ODATA and prevent error
     */
    private $active;


    public function setViewName($viewName)
    {
        return $this->viewName = $viewName;
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }


    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setActive(string $active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * @return bool
     */
    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

}