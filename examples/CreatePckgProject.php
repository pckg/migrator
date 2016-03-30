<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreatePckgProject
 *
 */

class CreatePckgProject extends Command
{

    protected function configure()
    {
        $this->setName('test:test')
            ->setDescription('Test command')
            ->addArgument('app', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $appArg = $input->getArgument('app');
        if (!$appArg) {
            $question = new Question('Name of app: ');
            $bundle = $helper->ask($input, $output, $question);
            if (!$bundle) {
                echo "Empty answer";
            } else {
                echo "Answered: " . $bundle;
            }
        } else {
            echo "Has predefined 'app' argument: " . $input->getArgument('app');
        }
        echo "\n";
    }

}

$application = new Application();
$application->add(new CreatePckgProject());
$application->run();