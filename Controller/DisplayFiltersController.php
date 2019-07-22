<?php

namespace W3com\HulkBundle\Controller;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use W3com\HulkBundle\Form\DisplayFilterType;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Service\DisplayFilterProvider;

class DisplayFiltersController extends AbstractController
{

    const INTERVAL_URL_KEY = 'interval_';

    private $displayProvider;

    private $request;


    public function __construct(DisplayFilterProvider $displayProvider, RequestStack $requestStack)
    {
        $this->displayProvider = $displayProvider;
        $this->request = $requestStack;
    }

    /**
     * @param $filename
     * @return Response
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function filtersForm($filename)
    {
        /** @var DataTable $display */
        $display = $this->displayProvider->getDisplayFilters($filename);

        if (!$display->getError()->isClassExist()) {
            return $this->redirectToRoute('w3com_update_project_entity', ['filename' => $filename]);
        }


        $form = $this->createForm(DisplayFilterType::class, $display);
        $form->handleRequest($this->request->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $this->request->getCurrentRequest()->request->all();
            $routeParams = $this->createRouteParams($formData, $display);

            return $this->redirectToRoute('w3com_display', $routeParams);
        }

        return $this->render('@W3comHulk/display/display_form_filter.html.twig', [
            'form' => $form->createView(), 'filename' => $filename, 'display' => $display
        ]);
    }

    private function createRouteParams(array $formData, DataTable $dataTable)
    {

        $routeParams = [];
        foreach ($formData['display_filter'] as $field => $value) {

            if ($value != null && substr($field, 0, 9) !== '_interval' &&
                $field !== 'submit' && $field !== '_token') {
                $routeParams[$field] = $value;
            }

            if (substr($field, 0, 9) === '_interval') {

                if ($value['min'] != "" ||$value['max'] != ""){
                    $fieldName = substr($field, 9);
                    $routeParams[self::INTERVAL_URL_KEY.$fieldName] = array_values($value)[0] . '|' .
                        array_values($value)[1];
                }

            }
        }

        $routeParams['filename'] = $dataTable->getDisplayName();
        return $routeParams;
    }

}