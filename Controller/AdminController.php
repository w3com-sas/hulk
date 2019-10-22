<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use W3com\BoomBundle\Generator\SLInspector;
use W3com\BoomBundle\RestClient\OdataRestClient;
use W3com\BoomBundle\RestClient\SLRestClient;
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

    public function __construct(AuthenticationUtils $authenticationUtils, EntityProvider $provider, DisplayProvider $displayProvider,
                                AdapterInterface $adapter, BoomGenerator $generator)
    {
        $this->cache = $adapter;
        $this->generator = $generator;
        $this->displayProvider = $displayProvider;
        $this->entityProvider = $provider;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function login()
    {
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $error = $this->authenticationUtils->getLastAuthenticationError();
        return $this->render('@W3comHulk/admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function calculationViews()
    {
        $this->generator->getOdsInspector()->getEntities();

    }

    public function sapTables()
    {

    }

    public function checkEntities()
    {
        $this->checkUser();
        $entities = $this->entityProvider->getEntities();
        return $this->render('@W3comHulk/admin/entities.html.twig', $entities);
    }

    public function displays()
    {
       // $this->checkUser();
        $data = json_decode($this->displayProvider->getJsonFinder()->getOnlineJson('configuration'), true);
        $displays = [];
        foreach ($data['displays'] as $display) {
            $displays[] = $this->displayProvider->getDisplay($display, [], 1);
        }
        return $this->render('@W3comHulk/admin/displays.html.twig', ['displays' => $displays]);
    }


    private function checkUser()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface || !in_array('ROLE_ADMIN_HULK', $user->getRoles())) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }
}