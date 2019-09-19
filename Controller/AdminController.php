<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Service\DisplayProvider;

class AdminController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var BoomGenerator
     */
    private $generator;
    /**
     * @var DisplayProvider
     */
    private $displayProvider;

    public function __construct(AuthenticationUtils $authenticationUtils, BoomGenerator $generator,
                                DisplayProvider $displayProvider)
    {
        $this->displayProvider = $displayProvider;
        $this->generator = $generator;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function login()
    {
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $error = $this->authenticationUtils->getLastAuthenticationError();
        return $this->render('@W3comHulk/admin/login.html.twig',[
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function calculationView()
    {
        $this->checkUser();
        $this->generator->getAppInspector()->initProjectEntities();
        $entities = $this->generator->getAppInspector()->getProjectEntities();
        return $this->render('@W3comHulk/admin/admin.html.twig', ['entities' => $entities]);
    }

    public function displays()
    {
        $this->displayProvider->getDisplays();
    }

    private function checkUser()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }
}