<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use W3com\BoomBundle\Service\BoomManager;

class CreateViewController extends AbstractController
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
    public function createView($filename)
    {

        $createdEntities = $this->boom->getGenerator()->createViewSchema();
        return $this->render('@W3comHulk/update.html.twig',
            ['createdEntities' => $createdEntities, 'filename' => $filename]);
    }
}