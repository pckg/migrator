<?php namespace Pckg\Migration\Provider;

use Pckg\Framework\Provider;
use Pckg\Migration\Console\RunMigrator;
use Pckg\Migration\Console\ShowMigrations;

class Config extends Provider
{

    public function commands()
    {
        return [
            RunMigrator::class,
            ShowMigrations::class,
        ];
    }

}