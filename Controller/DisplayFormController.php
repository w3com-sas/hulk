<?php

namespace W3com\HulkBundle\Controller;

use Doctrine\Common\Annotations\AnnotationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\HulkBundle\Form\DisplayType;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\DisplayFormProvider;
use W3com\HulkBundle\Url\UrlManager;

class DisplayFormController extends AbstractController
{
    /**
     * @var DisplayFormProvider
     */
    private $displayProvider;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(DisplayFormProvider $displayProvider, RequestStack $requestStack, UrlManager $urlManager, LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        $form = $this->createForm(DisplayType::class, $display);
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $this->request->getCurrentRequest()->request->all();
            $routeParams = $this->urlManager->createRouteParams($formData, $display);
            return $this->redirectToRoute('w3com_display', $routeParams);
        }
        return $this->render('@W3comHulk/display_form/form.html.twig', [
            'form' => $form->createView(), 'filename' => $filename, 'display' => $display
        ]);
    }

    /**
     * @return JsonResponse|Response
     */
    public function displayFormReload()
    {
        $postRequest = $this->request->getCurrentRequest()->request;
        if (!$postRequest->has('calcView')){
            return new JsonResponse('Calculation view param required', 400);
        }

        $choices = $postRequest->has('selectedChoices') ? $postRequest->get('selectedChoices') : [];
        $calculationView = $postRequest->get('calcView');

        try {
            $data = $this->displayProvider->getDataFromChoices($calculationView, $choices);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            return new JsonResponse(null, 500);
        }
        return new JsonResponse($data);
    }
}