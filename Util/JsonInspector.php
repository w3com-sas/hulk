<?php

namespace W3com\HulkBundle\Util;

class JsonInspector
{
    /** @var array */
    private $allFiles;

    /** @var array */
    private $brokenFiles;

    /**
     * @return array
     */
    public function getAllFiles(): array
    {
        return $this->allFiles;
    }

    /**
     * @param $file
     */
    public function addFile($file): void
    {
        $this->allFiles[] = $file;
    }

    /**
     * @return array
     */
    public function getBrokenFiles(): array
    {
        return $this->brokenFiles;
    }

    /**
     * @param array $brokenFile
     */
    public function addBrokenFile($brokenFile): void
    {
        $this->brokenFiles[] = $brokenFile;
    }

}