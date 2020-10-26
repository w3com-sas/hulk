<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Service\DisplayProvider;
use W3com\HulkBundle\Util\EntityProvider;

class AdminController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var EntityProvider
     */
    private $entityProvider;
    /**
     * @var DisplayProvider
     */
    private $displayProvider;
    /**
     * @var AdapterInterface
     */
    private $cache;
    /**
     * @var BoomGenerator
     */
    private $generator;
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(AuthenticationUtils $authenticationUtils, DisplayProvider $displayProvider,
                                AdapterInterface $adapter, BoomGenerator $generator, KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->cache = $adapter;
        $this->generator = $generator;
        $this->displayProvider = $displayProvider;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function login()
    {
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $error = $this->authenticationUtils->getLastAuthenticationError();

        return $this->render('@W3comHulk/admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    public function displays()
    {
        $data = json_decode($this->displayProvider->getJsonFinder()->getOnlineJson('configuration'), true);
        $displays = [];
        foreach ($data['displays'] as $display) {
            $displays[] = $this->displayProvider->getDisplay($display, [], 1);
        }

        return $this->render('@W3comHulk/admin/displays.html.twig', ['displays' => $displays]);
    }

    public function updateDisplays()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(true);
        $clearCache = new ArrayInput(['command' => 'boom:cl']);
        $updateDisplays = new ArrayInput(['command' => 'hulk:update:displays']);
        $application->run($clearCache, new NullOutput());
        $application->run($updateDisplays, new NullOutput());
        die();

        return $this->redirectToRoute('w3com_admin_displays');
    }

    public function displayForms()
    {
        $data = json_decode($this->displayProvider->getJsonFinder()->getOnlineJson('configuration'), true);
        $displays = [];
        foreach ($data['displays-form'] as $display) {
            $displays[] = $this->displayProvider->getDisplay($display, [], 1);
        }

        return $this->render('@W3comHulk/admin/displays.html.twig', ['displays' => $displays]);
    }

    public function entities()
    {
        $entities = $this->entityProvider->getEntities();

        return $this->render('@W3comHulk/admin/entities.html.twig', $entities);
    }
}
