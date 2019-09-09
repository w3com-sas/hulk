<?php

namespace W3com\HulkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Form\DisplayFilterType;
use W3com\HulkBundle\Model\App;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\DisplayFormProvider;
use W3com\HulkBundle\Service\DisplayProvider;
use W3com\HulkBundle\Url\UrlManager;

class AppController extends AbstractController
{

    private $request;

    private $logger;

    private $jsonFinder;

    private $serializer;
    /**
     * @var DisplayProvider
     */
    private $displayProvider;
    /**
     * @var DisplayFormProvider
     */
    private $displayForm;
    /**
     * @var UrlManager
     */
    private $urlManager;

    public function __construct(RequestStack $request, LoggerInterface $logger, JsonFinder $jsonFinder, DisplayProvider $display,
                                DisplayFormProvider $displayFormProvider, UrlManager $urlManager)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->jsonFinder = $jsonFinder;
        $this->request = $request;
        $this->logger = $logger;
        $this->displayProvider = $display;
        $this->displayForm = $displayFormProvider;
        $this->urlManager = $urlManager;
    }

    public function runApp($fileApp, $screenName = null)
    {
        $display = new Display();
        $file = $this->jsonFinder->getOnlineJson($fileApp, $display);
        $app = $this->serializer->deserialize($file, App::class, 'json');
        if ($screenName === null) {
            return $this->redirectToRoute('w3com_app_module', ['fileApp' => $fileApp, 'screenName' => $app->modules['screen']]);
        }
        $module = $this->getModuleByScreenName($screenName, $app->modules);
        if ($module['module'] === 'display-form'){
            return $this->displayForm($module);
        }

    }

    private function displayForm($module)
    {
        $display = $this->displayProvider->getDisplay($module['filename']);
        $form = $this->createForm(DisplayFilterType::class, $display);
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $this->request->getCurrentRequest()->request->all();
            $routeParams = $this->urlManager->createRouteParams($formData, $display);
            return $this->redirectToRoute('w3com_display', $routeParams);
        }
        return $this->render('@W3comHulk/display/display_form_filter.html.twig', [
            'form' => $form->createView(), 'filename' => $module['filename'], 'display' => $display
        ]);
    }

    private function getModuleByScreenName($screenName, $children)
    {
        foreach ($children as $module) {
            if ($module['screen'] === $screenName) {
                return $module;
            }
            if (array_key_exists('children', $module)) {
                return $this->getModuleByScreenName($screenName, $module['children']);
            }
        }
        return null;
    }


}