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
    public function display($filename)
    {
        $display = $this->displayProvider->getDisplay($filename, $this->request->getCurrentRequest()->query);
        return $this->render('@W3comHulk/display/all.html.twig',
            ['display' => $display, 'filename' => $filename]);
    }

}