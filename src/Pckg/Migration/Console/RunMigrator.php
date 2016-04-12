<?php namespace Pckg\Migration\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunMigrator extends Command
{

    protected function configure()
    {
        $this->setName('migrator:run')
            ->setDescription('Execute migrations one by one');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        die("RunMigrations::exec");
    }

}