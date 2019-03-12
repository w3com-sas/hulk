<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\HulkBundle\Service\TableProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DisplayController extends AbstractController
{

    private $tableProvider;

    private $logger;

    private $request;


    public function __construct(TableProvider $provider,
                                LoggerInterface $logger, RequestStack $request)
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
        }

        return $this->render('@W3comHulk/display.html.twig', ['table' => $table, 'filename' => $filename]);
    }


}