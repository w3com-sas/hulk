<?php

namespace W3com\HulkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(BoomGenerator $generator, AdapterInterface $cache)
    {
        $this->cache = $cache;
        $this->generator = $generator;
    }

    /**
     * @return Response
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function updateView()
    {
        $this->cache->deleteItem('ods.metadata');
        $createdEntities = $this->generator->createViewSchema();
        $updatedEntities = $this->generator->updateViewSchema();
        return new JsonResponse(
            [
                'updatedEntities' => $updatedEntities,
                'createdEntities' => $createdEntities
            ]);
    }
}