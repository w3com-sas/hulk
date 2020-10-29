<?php


namespace W3com\HulkBundle\Service;


use Exception;
use W3com\BoomBundle\Service\BoomManager;

class GlobalActionDataProvider
{
    /**
     * @var BoomManager
     */
    private $boomManager;

    public function __construct(BoomManager $boomManager)
    {
        $this->boomManager = $boomManager;
    }

    /**
     * @throws Exception
     */
    public function dataSelectProvider(string $entityName): array
    {
        $dataSelects = [];
        $entityResults = $this->boomManager->getRepository($entityName)->findAll();

        foreach ($entityResults as $key => $entityResult) {
            $dataSelects[$key] = $entityResult;
        }

        return $dataSelects;
    }
}