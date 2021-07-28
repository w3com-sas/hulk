<?php

namespace W3com\HulkBundle\Controller;

use Exception;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use W3com\HulkBundle\Service\DisplayProvider;

class AdminController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var DisplayProvider
     */
    private $displayProvider;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(AuthenticationUtils $authenticationUtils, DisplayProvider $displayProvider, KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->displayProvider = $displayProvider;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function login(): Response
    {
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $error = $this->authenticationUtils->getLastAuthenticationError();

        return $this->render('@W3comHulk/admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function displays(): Response
    {
        $data = json_decode($this->displayProvider->getJsonFinder()->getOnlineJson('configuration'), true);
        $displays = [];

        foreach ($data['displays'] as $display) {
            $displays[] = $this->displayProvider->getDisplay($display, [], 1);
        }

        return $this->render('@W3comHulk/admin/displays.html.twig', ['displays' => $displays]);
    }

    /**
     * TODO : deactivate
     *
     * @throws Exception
     */
    public function updateDisplays(): ?RedirectResponse
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(true);
        $clearCache = new ArrayInput(['command' => 'boom:cl']);
        $updateDisplays = new ArrayInput(['command' => 'hulk:update:displays']);
        $application->run($clearCache, new NullOutput());
        $application->run($updateDisplays, new NullOutput());

        return null;

        //return $this->redirectToRoute('w3com_admin_displays');
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function displayForms(): Response
    {
        $data = json_decode($this->displayProvider->getJsonFinder()->getOnlineJson('configuration'), true);
        $displays = [];

        foreach ($data['displays-form'] as $display) {
            $displays[] = $this->displayProvider->getDisplay($display, [], 1);
        }

        return $this->render('@W3comHulk/admin/displays.html.twig', ['displays' => $displays]);
    }
}
