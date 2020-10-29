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

    /** @var bool */
    private $fileIsBroken;

    /** @var array */
    private $filterErrors = [];

    /** @var array */
    private $columnErrors = [];

    /** @var array */
    private $requestParamsErrors = [];

    /** @var array */
    private $urlErrors = [];

    private $entityErrors = [];

    /**
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

    public function setNonexistentProperties(Display $dataTable)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {
            if ('Y' !== $column->getActive()) {
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

    public function setClassExist(bool $classExist): Error
    {
        $this->classExist = $classExist;

        return $this;
    }

    /**
     * @return bool
     */
    public function isViewExist(): ?bool
    {
        return $this->viewExist;
    }

    public function setViewExist(bool $viewExist): void
    {
        $this->viewExist = $viewExist;
    }

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

    public function hasErrorFilter()
    {
        return count($this->filterErrors) > 0;
    }

    public function getUrlErrors(): array
    {
        return $this->urlErrors;
    }

    /**
     * @return bool
     */
    public function isFileIsBroken(): ?bool
    {
        return $this->fileIsBroken;
    }

    public function setFileIsBroken(bool $fileIsBroken): void
    {
        $this->fileIsBroken = $fileIsBroken;
    }

    public function getEntityErrors(): array
    {
        return $this->entityErrors;
    }

    /**
     * @param $entityError
     */
    public function addEntityErrors($entityError): void
    {
        $this->entityErrors[] = $entityError;
    }

    public function all()
    {
        return array_merge($this->entityErrors, $this->urlErrors, $this->columnErrors, $this->filterErrors);
    }
}
