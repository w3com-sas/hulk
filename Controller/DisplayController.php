<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function display($filename)
    {

        $requestParams = $this->request->getCurrentRequest()->query;
        $table = $this->tableProvider->getDataTable($filename, $requestParams);


        if (!$table->getError()->isClassExist() && $table->getError()->isFileExist()) {
            return $this->redirectToRoute('w3com_create_view', ['filename' => $filename]);
        } elseif ($table->getError()->hasErrorColumn()) {
            return $this->redirectToRoute('w3com_update_project_entity', ['filename' => $filename]);
        }

        return $this->render('@W3comHulk/display/all.html.twig',
            ['table' => $table, 'filename' => $filename]);
    }


}