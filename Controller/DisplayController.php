<?php

namespace W3com\HulkBundle\Controller;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\DisplayProvider;

class DisplayController extends AbstractController
{
    /**
     * @var DisplayProvider
     */
    private $displayProvider;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(DisplayProvider $provider, RequestStack $request, KernelInterface $kernel)
    {
        $this->displayProvider = $provider;
        $this->request = $request;
        $this->kernel = $kernel;
    }

    /**
     * @throws InvalidArgumentException|ReflectionException
     */
    public function display($filename): Response
    {
        $display = $this->displayProvider->getDisplay(
            $filename,
            $this->request->getCurrentRequest()->query
        );

        return $this->render('@W3comHulk/display/all.html.twig', [
            'display' => $display,
            'filename' => $filename,
        ]);
    }

    /**
     * @throws Exception
     */
    public function cacheClear(): Response
    {
        // the number determine the message in the view.
        $commandResponse = 0;
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'hulk:clear',
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        // $output->fetch() contains the console output of command.
        // That why the condition is searching for a needle in this input.
        if (strpos($output->fetch(), 'Failed to clear Hulk cache.')) {
            $commandResponse = 1;
        } elseif (strpos($output->fetch(), 'Failure during clear Boom cache.')) {
            $commandResponse = 2;
        } elseif (
            strpos($output->fetch(), 'Failed to clear Hulk cache.')
            || strpos($output->fetch(), 'Failure during clear Boom cache.')
        ) {
            $commandResponse = 3;
        }

        return $this->render('@W3comHulk/display/cache_clear.html.twig', [
            'commandResponse' => $commandResponse,
            // base.html.twig need the following parameter
            'display' => new Display(),
        ]);
    }
}
