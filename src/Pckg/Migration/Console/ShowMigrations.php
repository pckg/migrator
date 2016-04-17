<?php namespace Pckg\Migration\Console;

use Pckg\Framework\Console\Command;

class ShowMigrations extends Command
{

    protected function configure()
    {
        $this->setName('migrator:show')
            ->setDescription('List available migrations');
    }

    public function handle()
    {
        die("ListMigrations::exec");
    }

}