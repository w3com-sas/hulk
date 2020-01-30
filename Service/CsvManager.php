<?php

namespace W3com\HulkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;

class CsvManager
{
    private $serializer;

    private $request;

    private $dataTable;

    private $displayProvider;

    public function __construct(DisplayProvider $displayProvider, RequestStack $request)
    {
        $this->displayProvider = $displayProvider;
        $this->request = $request;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $this->dataTable = new Display();
    }

    public function getCsv(array $data, string $filename)
    {

        //$data = $this->request->getCurrentRequest()->request->all();
        $file = $this->displayProvider->getJsonFinder()->getOnlineJson($filename, $this->dataTable);
        $dataTable = $this->displayProvider->getConstructor()->hydrate($this->dataTable, $file);
        $formattedData = [];

        foreach ($data as $dataLine) {
            $line = [];
            foreach ($dataLine as $fieldName => $value){
                /** @var Column $column */
                foreach ($dataTable->getColumns() as $column) {
                    if ($column->getFieldName() === $fieldName && $column->getType() === Column::COL_TYPE_TEXT && !$column->isHidden()) {
                        $line[$column->getLabel()] = $value;
                    }
                }
            }
            $formattedData[] = $line;
        }
        return $this->serializer->encode($formattedData, 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
    }

    public function getDisplayName()
    {
        return $this->dataTable->getDisplayName();
    }
}