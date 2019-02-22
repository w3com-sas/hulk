<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use W3com\HulkBundle\Service\SessionManager;
use W3com\HulkBundle\Service\TableProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JsonTableController extends AbstractController
{

    private $tableProvider;

    private $logger;

    private $request;

    private $sessionManager;

    public function __construct(TableProvider $provider,SessionManager $session,
                                LoggerInterface $logger, RequestStack $request)
    {
        $this->tableProvider = $provider;
        $this->sessionManager = $session;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function jsonTableView($filename)
    {
        $requestParams = $this->request->getCurrentRequest()->query;
        $table = $this->tableProvider->getDataTable($filename, $requestParams);

        if (!$table->getError()->isClassExist() && $table->getError()->isFileExist()) {
            return $this->redirectToRoute('w3com_create_view', ['filename' => $filename]);
        }

        $this->sessionManager->initSession($filename);
        return $this->render('@W3comHulk/display.html.twig', ['table' => $table, 'filename' => $filename]);
    }

    /**
     * @return JsonResponse
     */
    public function storeInSession()
    {
        $data = $this->request->getCurrentRequest()->request->get('sessionData');
        try {
            $this->sessionManager->addStructureInfo();
        } catch (\Exception $e) {
            $this->logger->error('Session store bug : ' . $e->getMessage(), $e->getTrace());
            return new JsonResponse(null, 500);
        }
        return new JsonResponse('Successfuly stored ' . $data['key'] . ' in session', 200);
    }


}