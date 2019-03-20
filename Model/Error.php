<?php

namespace W3com\HulkBundle\Model;

class Error
{
    const ERROR_MISSING_FIELD = 'Le champ %s n\'existe pas dans la calculation view %s';

    /** @var mixed */
    private $nonexistentProperties;

    /** @var bool */
    private $classExist;

    /** @var bool */
    private $fileExist;

    /** @var bool */
    private $viewExist;

    /** @var array */
    private $filterErrors = [];

    /** @var array */
    private $columnErrors = [];

    /** @var array */
    private $requestParamsErrors = [];

    /** @var array */
    private $urlErrors = [];

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

    /**
     * @return bool
     */
    public function isViewExist(): ?bool
    {
        return $this->viewExist;
    }

    /**
     * @param bool $viewExist
     */
    public function setViewExist(bool $viewExist): void
    {
        $this->viewExist = $viewExist;
    }

    /**
     * @return array
     */
    public function getFilterErrors(): array
    {
        return $this->filterErrors;
    }

    /**
     * @param $filterError
     */
    public function addFilterError($filterError): void
    {
        $this->filterErrors[] = $filterError;
    }

    /**
     * @return array
     */
    public function getColumnErrors(): array
    {
        return $this->columnErrors;
    }

    /**
     * @param $columnError
     */
    public function addColumnError($columnError): void
    {
        $this->columnErrors[] = $columnError;
    }

    public function addUrlError($column, $urlError)
    {
        $this->urlErrors[$column] = $urlError;
    }

    /**
     * @return array
     */
    public function getRequestParamsErrors(): array
    {
        return $this->requestParamsErrors;
    }

    /**
     * @param $requestParamsError
     */
    public function addRequestParamsError($requestParamsError): void
    {
        $this->requestParamsErrors[] = $requestParamsError;
    }

    /**
     * @return bool
     */
    public function hasErrorColumn()
    {
        return count($this->columnErrors) > 0;
    }
}