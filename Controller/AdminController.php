<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use W3com\HulkBundle\Service\JsonFileManager;

class AdminController extends AbstractController
{
    private $jsonFileManager;

    private $logger;

    public function __construct(JsonFileManager $fileManager, LoggerInterface $logger)
    {
        $this->jsonFileManager = $fileManager;
        $this->logger = $logger;
    }

    public function adminDashboard()
    {
        $inpector = $this->jsonFileManager->inspectJsonFiles();
        dump($inpector);
        return $this->render('@W3comHulk/admin/dashboard.html.twig');
    }
}