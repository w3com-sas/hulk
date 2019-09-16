<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\BoomBundle\Service\BoomManager;

class UpdateProjectEntityController extends AbstractController
{
    /**
     * @var BoomGenerator
     */
    private $generator;

    public function __construct(BoomGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function updateView()
    {
        $createdEntities = $this->generator->createViewSchema();
        $updatedEntities = $this->generator->updateViewSchema();
        return new JsonResponse(
            [
                'updatedEntities' => $updatedEntities,
                'createdEntities' => $createdEntities
            ]);
    }
}