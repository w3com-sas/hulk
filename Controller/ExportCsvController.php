<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use W3com\HulkBundle\Service\CsvManager;

class ExportCsvController extends AbstractController
{
    private $csvManager;

    private $request;

    public function __construct(RequestStack $request, CsvManager $csvManager)
    {
        $this->request = $request;
        $this->csvManager = $csvManager;
    }

    public function exportCsv()
    {
        $data = json_decode($this->request->getCurrentRequest()->getContent(), true);
        $filename = $this->request->getCurrentRequest()->query->get('filename');

        $response = new Response(mb_convert_encoding( $this->csvManager->getCsv($data, $filename), 'Windows-1252', 'UTF-8'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename.'.csv'
        );

        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
