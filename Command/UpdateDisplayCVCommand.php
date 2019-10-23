<?php

namespace W3com\HulkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use W3com\BoomBundle\Exception\EntityNotFoundException;
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
        $io = new SymfonyStyle($input, $output);
        $io->title('Hulk update CV with display\'s configuration');

        $display = new Display();
        $config = json_decode($this->jsonFinder->getOnlineJson('configuration', $display), true);

        if (!$display->getError()->isFileExist()) {
            throw new \Exception('Impossible de retrouver le fichier de configuration des displays.');
        }

        $calculationViews = [];
        $errors = [];
        foreach (array_merge($config['displays'], $config['display-forms']) as $displayFilename) {
            $cv = \json_decode($this->jsonFinder->getOnlineJson($displayFilename, $display), true);
            if (!$display->getError()->isFileExist()) {
                $errors[$displayFilename] = 'ERROR : File not exist or is broken.';
                continue;
            }
            $calculationViews[$cv['CalculationView']] = $cv['CalculationView'];
        }

        $continue = true;
        if (count($errors) > 0) {
            $continue = $io->confirm('There are nonexistent display in configuration file. Do 
            you want to continue ?');
        }

        if ($continue) {
            foreach ($calculationViews as $calculationView) {
                if (substr($calculationView, 0, 5) !== 'ERROR') {
                    try {
                        $this->generator->createViewEntity($calculationView);
                    } catch (EntityNotFoundException $exception) {
                        $io->error('Unable to find ' . $calculationView);
                        continue;
                    } catch (\Exception $e) {
                        $io->error('Unknow error when trying to update ' . $calculationView);
                        continue;
                    }
                    $io->success($calculationView . ' created.');
                }
            }
        }
    }
}