<?php

namespace W3com\HulkBundle\Command;

use Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use W3com\HulkBundle\Service\CacheManager;
use Symfony\Component\Console\Command\Command;

class ClearCacheCommand extends Command
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('hulk:clear')
            ->setDescription('Clear Hulk + Boom cache.')
        ;
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $returnValue = 0;

        $deletedHulkCache = $this->cacheManager->clearCache();

        $command = $this->getApplication()->find('boom:clear');
        $arguments = ['isFromHulk' => true];
        $commandInput = new ArrayInput($arguments);

        $deletedBoomCache = $command->run($commandInput, $output);

        if ($deletedHulkCache) {
            $io->success('Hulk cache successfully cleared.');
        } else {
            $io->error('Failed to clear Hulk cache.');
            $returnValue = 1;
        }

        if ($deletedBoomCache === 1) {
            $returnValue = 1;
        }

        return $returnValue;
    }
}