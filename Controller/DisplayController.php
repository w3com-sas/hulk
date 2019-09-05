<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\DisplayProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DisplayController extends AbstractController
{

    private $displayProvider;

    private $logger;

    private $request;

    public function __construct(DisplayProvider $provider, LoggerInterface $logger, RequestStack $request)
    {
        $this->displayProvider = $provider;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * @param $filename
     * @return Response
     * @throws \Exception
     */
    public function displayDev($filename)
    {
        $table = $this->displayInit($filename);
        return $this->render('@W3comHulk/display/all.html.twig',
            ['table' => $table, 'filename' => $filename, 'debug' => true]);
    }

    /**
     * @param $filename
     * @return Response
     * @throws \Exception
     */
    public function displayProd($filename)
    {
        $table = $this->displayInit($filename);
        return $this->render('@W3comHulk/display/all.html.twig',
            ['table' => $table, 'filename' => $filename]);
    }

    /**
     * @param $filename
     * @return RedirectResponse|Display
     * @throws \Exception
     */
    private function displayInit($filename)
    {
        $getRequestParams = $this->request->getCurrentRequest()->query;
        $table = $this->displayProvider->getDisplay($filename, $getRequestParams);
        return $table;
    }
}