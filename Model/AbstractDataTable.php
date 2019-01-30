<?php

namespace W3com\HulkBundle\Model;


abstract class AbstractDataTable
{
    protected $displayName;

    protected $calcView;

    protected $entity;

    protected $nonexistentProperties;

    protected $classExist;

    protected $fileExist;

    public function setDisplayName($displayName)
    {
        return $this->displayName = $displayName;
    }

    public function setCalcView($calcView)
    {
        $formatedView = str_replace('/', '', $calcView);
        return $this->calcView = $formatedView;
    }

    /**
     * @return mixed
     */
    public function getCalcView()
    {
        return $this->calcView;
    }


    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param bool $fileExist
     * @return AbstractDataTable
     */
    public function setFileExist(bool $fileExist)
    {
        $this->fileExist = $fileExist;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFileExist(): ?bool
    {
        return $this->fileExist;
    }

    /**
     * @param DataTable $dataTable
     */
    public function setNonexistentProperties(DataTable $dataTable)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column){

            if ($column->getActive() !== 'Y'){
                $this->nonexistentProperties[] = $column;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getNonexistentProperties()
    {
        return $this->nonexistentProperties;
    }

    /**
     * @return bool
     */
    public function isClassExist(): ?bool
    {
        return $this->classExist;
    }

    /**
     * @param bool $classExist
     */
    public function setClassExist(bool $classExist): void
    {
        $this->classExist = $classExist;
    }


    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

}