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
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\CsvManager;
use W3com\HulkBundle\Service\DisplayProvider;

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
        $response = new Response($this->csvManager->getCsv($data, $filename));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->csvManager->getDisplayName().'.csv'
        );

        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

}