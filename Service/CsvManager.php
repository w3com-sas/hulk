<?php

namespace W3com\HulkBundle\Service;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        // TODO : pour l'instant ne gère qu'un seul export csv. Si besoin de plusieurs passer la param index par exemple
        /** @var GlobalAction $globalAction */
        $globalAction = $display->getGlobalActionsByType('export-csv')[0];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Feuille');
        $headers = [];
        $rowCounter = 2;
        foreach ($data as $dataLine) {
            $columnCounter = 1;
            if (count($globalAction->getFields()) > 0) {
                foreach ($globalAction->getFields() as $field) {
                    $sheet->setCellValueByColumnAndRow(
                        $columnCounter,
                        $rowCounter,
                        array_key_exists($field, $dataLine) ? $dataLine[$field] : 'Champ inconnu'
                    );
                    $headers[$columnCounter] = $field;
                    $columnCounter++;
                }
            } else {
                /** @var Column $column */
                foreach ($display->getColumns() as $column) {
                    foreach ($dataLine as $fieldName => $value) {
                        if ($column->getFieldName() === $fieldName && Column::COL_TYPE_TEXT === $column->getType() && !$column->isHidden()) {
                            $sheet->setCellValueByColumnAndRow(
                                $columnCounter,
                                $rowCounter,
                                $value
                            );
                            $headers[$columnCounter] = $column->getLabel();
                        }
                    }
                    $columnCounter++;
                }
            }
            $rowCounter++;
        }
        foreach ($headers as $index=>$header){
            $sheet->setCellValueByColumnAndRow(
                $index,
                1,
                $header
            );
        }

        $sheet->getStyle('A1:'.Coordinate::stringFromColumnIndex($columnCounter - 1).(string) ($rowCounter - 1))
            ->getAlignment()->setWrapText(false);
        for ($iterator = 1; $iterator < $columnCounter; ++$iterator) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($iterator))->setAutoSize(true);
        }
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}
