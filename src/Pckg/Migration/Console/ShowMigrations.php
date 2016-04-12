<?php namespace Pckg\Migration\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowMigrations extends Command
{

    protected function configure()
    {
        $this->setName('migrator:show')
            ->setDescription('List available migrations');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        die("ListMigrations::exec");
    }

}