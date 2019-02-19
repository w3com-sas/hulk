<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use W3com\HulkBundle\Service\TableProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JsonTableController extends AbstractController
{

    private $tableProvider;

    private $logger;

    public function __construct(TableProvider $provider, LoggerInterface $logger)
    {
        $this->tableProvider = $provider;
        $this->logger = $logger;
    }

    /**
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function jsonTableView($filename)
    {

        $table = $this->tableProvider->getDataTable($filename);

        if(!$table->getError()->isClassExist() && $table->getError()->isFileExist()){
            return $this->redirectToRoute('w3com_create_view', ['filename' => $filename]);
        }
        return $this->render('@W3comHulk/display.html.twig', ['table' => $table, 'filename' => $filename]);
    }

}