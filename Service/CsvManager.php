<?php

namespace W3com\HulkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\GlobalAction;

class CsvManager
{
    private $serializer;

    private $request;

    private $displayProvider;

    private $displayName = 'export-csv';

    public function __construct(DisplayProvider $displayProvider, RequestStack $request)
    {
        $this->displayProvider = $displayProvider;
        $this->request = $request;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
    }

    public function getCsv(array $data, string $filename)
    {
        //$data = $this->request->getCurrentRequest()->request->all();
        $display = new Display();
        $file = $this->displayProvider->getJsonFinder()->getOnlineJson($filename, $display);
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
                foreach ($dataLine as $fieldName => $value) {
                    /** @var Column $column */
                    foreach ($display->getColumns() as $column) {
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

    public function getDisplayName()
    {
        return $this->displayName;
    }
}
