<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Service\DisplayProvider;

class ExportCsvController extends AbstractController
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
        $this->dataTable = new DataTable();
    }

    public function exportCsv()
    {
        //$data = $this->request->getCurrentRequest()->request->all();
        $data = json_decode($this->request->getCurrentRequest()->getContent(), true);
        $filename = $this->request->getCurrentRequest()->query->get('filename');

        $file = $this->displayProvider->getJsonFinder()->getOnlineJson($filename, $this->dataTable);
        $dataTable = $this->displayProvider->getConstructor()->hydrateDataTable($file, $this->dataTable);

        $formattedData = [];

        foreach ($data as $dataLine) {
            $line = [];
            foreach ($dataLine as $fieldName => $value){

                /** @var Column $column */
                foreach ($dataTable->getColumns() as $column) {

                    if ($column->getFieldName() === $fieldName && $column->getType() === Column::TYPE_TEXT) {
                        $line[$column->getLabel()] = $value;
                    }
                }
            }
            $formattedData[] = $line;
        }

        $csvContent = $this->serializer->encode($formattedData, 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
        $response = new Response($csvContent);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $dataTable->getDisplayName().'.csv'
        );

        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

}