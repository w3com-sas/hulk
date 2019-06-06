<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Service\DisplayProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DisplayController extends AbstractController
{

    private $tableProvider;

    private $logger;

    private $request;

    public function __construct(DisplayProvider $provider, LoggerInterface $logger, RequestStack $request)
    {
        $this->tableProvider = $provider;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * @param $filename
     * @return Response
     * @throws \Exception
     */
    public function display($filename)
    {
        $table = $this->displayInit($filename);

        if (!$table->getError()->isClassExist() && $table->getError()->isFileExist() ||
            count($table->getError()->getColumnErrors()) > 0) {
            return $this->redirectToRoute('w3com_update_project_entity', ['filename' => $filename]);
        }

        return $this->render('@W3comHulk/display/all.html.twig',
            ['table' => $table, 'filename' => $filename]);
    }

    /**
     * @param $filename
     * @return Response
     * @throws \Exception
     */
    public function debug($filename)
    {
        $table = $this->displayInit($filename);
        return $this->render('@W3comHulk/display/all.html.twig',
            ['table' => $table, 'filename' => $filename, 'debug' => true]);
    }

    /**
     * @param $filename
     * @return RedirectResponse|DataTable
     * @throws \Exception
     */
    private function displayInit($filename)
    {
        $getRequestParams = $this->request->getCurrentRequest()->query;
        $table = $this->tableProvider->getDataTable($filename, $getRequestParams);
        return $table;
    }
}