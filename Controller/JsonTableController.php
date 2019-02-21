<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use W3com\HulkBundle\Service\TableProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JsonTableController extends AbstractController
{

    private $tableProvider;

    private $logger;

    private $request;

    public function __construct(TableProvider $provider, LoggerInterface $logger, RequestStack $request)
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
    public function jsonTableView($filename)
    {
        $table = $this->tableProvider->getDataTable($filename);

        if(!$table->getError()->isClassExist() && $table->getError()->isFileExist()){
            return $this->redirectToRoute('w3com_create_view', ['filename' => $filename]);
        }

        $session = $this->getSession();
        $session->set('displayUrl', $this->request->getCurrentRequest()->getUri());
        return $this->render('@W3comHulk/display.html.twig', ['table' => $table, 'filename' => $filename]);
    }

    /**
     * @param $key
     * @param $value
     * @return JsonResponse
     */
    public function storeInSession($key, $value)
    {
        try {
            $session = $this->getSession();
            $session->set($key, $value);
        } catch (\Exception $e){
            $this->logger->error('Session bug : '.$e->getMessage(), $e->getTrace());
            return new JsonResponse(null, 500);
        }
        return new JsonResponse('Successfuly stored '.$key.' in session', 200);
    }

    private function getSession()
    {
        if (!$this->request->getCurrentRequest()->hasSession()){
            $session = new Session();
            $session->start();
        } else {
            $session = $this->request->getCurrentRequest()->getSession();
        }
        return $session;
    }

}