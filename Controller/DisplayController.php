<?php

namespace W3com\HulkBundle\Controller;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\DisplayProvider;

class DisplayController extends AbstractController
{
    private $displayProvider;

    private $request;

    public function __construct(DisplayProvider $provider, RequestStack $request)
    {
        $this->displayProvider = $provider;
        $this->request = $request;
    }

    /**
     * @throws InvalidArgumentException|ReflectionException
     */
    public function display($filename): Response
    {
        $display = $this->displayProvider->getDisplay($filename, $this->request->getCurrentRequest()->query);

        return $this->render(
            '@W3comHulk/display/all.html.twig',
            ['display' => $display, 'filename' => $filename]
        );
    }
}
