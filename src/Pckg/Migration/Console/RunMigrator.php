<?php namespace Pckg\Migration\Console;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class RunMigrator extends Command
{

    protected function configure()
    {
        $this->setName('migrator:run')
            ->setDescription('Execute migrations one by one')
            ->addArguments([
                'migration' => 'Migration class',
            ], InputArgument::REQUIRED);
    }

    public function handle()
    {
        die("RunMigrations::exec");
    }

}