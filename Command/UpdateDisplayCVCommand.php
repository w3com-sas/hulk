<?php

namespace W3com\HulkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use W3com\BoomBundle\Service\BoomGenerator;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Model\Display;

class UpdateDisplayCVCommand extends Command
{
    /**
     * @var BoomGenerator
     */
    private $generator;
    /**
     * @var JsonFinder
     */
    private $jsonFinder;

    public function __construct(BoomGenerator $generator, JsonFinder $jsonFinder)
    {
        $this->jsonFinder = $jsonFinder;
        $this->generator = $generator;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('hulk:update:displays')
            ->setDescription('Clear boom cache.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $display = new Display();
        $config = json_decode($this->jsonFinder->getOnlineJson('configuration', $display), true);
        if (!$display->getError()->isFileExist()){
            throw new \Exception('Impossible de retrouver le fichier de configuration des displays.');
        }

        $calculationViews = [];
        foreach ($config['displays'] as $displayFilename){

            try {
                $cv = \json_decode($this->jsonFinder->getOnlineJson($displayFilename));
                dump($cv);
            } catch (\Exception $e){
                $calculationViews[$displayFilename] = 'ERROR : File not exist or is broken.';
                continue;
            }
            $calculationViews[$displayFilename] = $cv['calculationView'];
        }

       foreach ($calculationViews as $calculationView){
           if (!substr($calculationView, 0, 5) === 'ERROR'){
               $this->generator->createViewEntity($calculationView);
           }
       }
    }
}