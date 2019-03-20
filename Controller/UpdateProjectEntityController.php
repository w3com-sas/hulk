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
        $updatedEntities = $this->boom->getGenerator()->updateViewSchema();
        $test = $this->boom->getGenerator()->getOdsInspector()->getOdsEntity('Facturation');
        $otherTest = $this->boom->getGenerator()->getAppInspector()->getProjectEntity('Facturation')
            ->getProperty('U_W3C_NOPV');
        dump($test, $otherTest);
        return $this->render('@W3comHulk/display/update.html.twig',
            ['updatedEntities' => $updatedEntities, 'filename' => $filename]);
    }
}