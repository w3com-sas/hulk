<?php

namespace W3com\HulkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use W3com\HulkBundle\Service\DisplayCachingManager;

class DisplayCachingCommand extends Command
{
    private $displayCachingManager;

    public function __construct(
        DisplayCachingManager $displayCachingManager
    )
    {
        $this->displayCachingManager = $displayCachingManager;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('hulk:launch-display-caching')
            ->setDescription('Launch display caching')
            ->addOption('display',null,InputOption::VALUE_OPTIONAL,'Specify an specific display')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output):int
    {
        $io = new SymfonyStyle($input,$output);

        if(false === $input->getOption('display') || null === $input->getOption('display')) {
            $displayName = '';
        } else {
            $displayName = $input->getOption('display');
        }

        try{
            $this->displayCachingManager->treat($displayName);
        } catch(\Exception $e){
            if($output->isVerbose()){
                $io->error('Erreur : '.$e->getMessage());
            }
        }

        return 1;
    }
}
