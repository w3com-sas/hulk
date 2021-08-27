<?php

namespace W3com\HulkBundle\Service;

use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\GlobalAction;

class CsvManager
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var DisplayProvider
     */
    private $displayProvider;

    /**
     * @var string
     */
    private $displayName = 'export-csv';

    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(DisplayProvider $displayProvider)
    {
        $this->displayProvider = $displayProvider;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $this->cacheManager = new CacheManager();
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function getCsv(array $data, string $filename)
    {
        $display = new Display();

        if (!$this->cacheManager->isInCache($filename)) {
            $file = $this->displayProvider->getJsonFinder()->getOnlineJson($filename, $display);
        } else {
            $cacheItem = $this->cacheManager->getCacheItem(CacheManager::DISPLAY_CACHE_KEY);
            $displays = $cacheItem->get();

            if (!array_key_exists($filename, $displays)) {
                $file = $this->displayProvider->getJsonFinder()->getOnlineJson($filename, $display);
            } else {
                $display->getError()->setFileExist(true);
                $file = $displays[$filename];
            }
        }

        $display = $this->displayProvider->getConstructor()->hydrate($display, $file);
        $this->displayName = $display->getDisplayName();
        $formattedData = [];

        // TODO : pour l'instant ne gère qu'un seul export csv. Si besoin de plusieurs passer la param index par exemple
        /** @var GlobalAction $globalAction */
        $globalAction = $display->getGlobalActionsByType('export-csv')[0];

        foreach ($data as $dataLine) {
            $line = [];

            if (count($globalAction->getFields()) > 0) {
                foreach ($globalAction->getFields() as $field) {
                    $line[$field] = array_key_exists($field, $dataLine) ? $dataLine[$field] : 'Champ inconnu';
                }
            } else {
                /** @var Column $column */
                foreach ($display->getColumns() as $column) {
                    foreach ($dataLine as $fieldName => $value) {
                        if ($column->getFieldName() === $fieldName && Column::COL_TYPE_TEXT === $column->getType() && !$column->isHidden()) {
                            $line[$column->getLabel()] = $value;
                        }
                    }
                }
            }

            $formattedData[] = $line;
        }

        return $this->serializer->encode($formattedData, 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}
