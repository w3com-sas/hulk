<?php

namespace W3com\HulkBundle\Controller;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use W3com\HulkBundle\Form\DisplayFilterType;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\DisplayFormProvider;
use W3com\HulkBundle\Url\UrlManager;

class DisplayFormController extends AbstractController
{
    private $displayProvider;

    private $request;

    private $urlManager;


    public function __construct(DisplayFormProvider $displayProvider, RequestStack $requestStack, UrlManager $urlManager)
    {
        $this->urlManager = $urlManager;
        $this->displayProvider = $displayProvider;
        $this->request = $requestStack;
    }

    /**
     * @param $filename
     * @return Response
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function displayForm($filename)
    {
        /** @var Display $display */
        $display = $this->displayProvider->getDisplay($filename);
        $form = $this->createForm(DisplayFilterType::class, $display);
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $this->request->getCurrentRequest()->request->all();
            $routeParams = $this->urlManager->createRouteParams($formData, $display);
            return $this->redirectToRoute('w3com_display', $routeParams);
        }
        return $this->render('@W3comHulk/display/display_form_filter.html.twig', [
            'form' => $form->createView(), 'filename' => $filename, 'display' => $display
        ]);
    }



}