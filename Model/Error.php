<?php

namespace W3com\HulkBundle\Model;

class Error
{
    protected $nonexistentProperties;

    protected $classExist;

    protected $fileExist;

    /**
     * @param bool $fileExist
     * @return Error
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
}