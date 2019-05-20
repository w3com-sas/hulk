<?php

namespace W3com\HulkBundle\Controller;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use W3com\HulkBundle\Form\DisplayFilterType;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Service\DisplayFilterProvider;

class DisplayFiltersController extends AbstractController
{

    private $displayProvider;


    public function __construct(DisplayFilterProvider $displayProvider)
    {
        $this->displayProvider = $displayProvider;
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
        $form = $this->createForm(DisplayFilterType::class, $display);
        return $this->render('@W3comHulk/display/display_form_filter.html.twig', [
            'form' => $form->createView(), 'filename' => $filename, 'display' => $display
        ]);
    }

}