<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use W3com\BoomBundle\Service\BoomManager;

class UpdateProjectEntityController extends AbstractController
{
    private $boom;

    public function __construct(BoomManager $boom)
    {
        $this->boom = $boom;
    }

    /**
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateView($filename)
    {
        $createdEntities = $this->boom->getGenerator()->createViewSchema();
        $updatedEntities = $this->boom->getGenerator()->updateViewSchema();

        return $this->render('@W3comHulk/display/update.html.twig',
            [
                'updatedEntities' => $updatedEntities,
                'createdEntities' => $createdEntities,
                'filename' => $filename
            ]);
    }
}