<?php

namespace W3com\HulkBundle\Controller;

use W3com\HulkBundle\Service\TableProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JsonTableController extends AbstractController
{

    private $tableProvider;

    public function __construct(TableProvider $provider)
    {
        $this->tableProvider = $provider;
    }

    /**
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function jsonTableView($filename)
    {
        $table = $this->tableProvider->getDataTable($filename);

        if(!$table->isClassExist() && $table->isFileExist()){
            return $this->redirectToRoute('w3com_create_view', ['filename' => $filename]);
        }

        return $this->render('@W3comHulk/tables.html.twig', ['table' => $table, 'filename' => $filename]);
    }

}