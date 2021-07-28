<?php

namespace W3com\HulkBundle\Controller;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use W3com\BoomBundle\Service\BoomGenerator;

class UpdateProjectEntityController extends AbstractController
{
    const TYPE_CV = 'CV';

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
     * @throws Exception
     * @throws InvalidArgumentException
     *
     * @return Response
     */
    public function updateView()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'bool:cl',
        ]);
        $output = new NullOutput();
        $application->run($input, $output);
        $this->cache->deleteItem('ods.metadata');
        $createdEntities = $this->generator->createViewSchema();
        $updatedEntities = $this->generator->updateViewSchema();

        return new JsonResponse(
            [
                'updatedEntities' => $updatedEntities,
                'createdEntities' => $createdEntities,
            ]);
    }

    public function createTable($tableName, $type)
    {
        try {
            if ($type === $this::TYPE_CV) {
                $this->generator->createViewEntity($tableName);
            } else {
                $this->generator->createSapEntity($tableName);
            }
        } catch (Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }

        return new JsonResponse(['success' => true]);
    }
}
