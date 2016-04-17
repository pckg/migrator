<?php namespace Pckg\Migration\Command;

use Pckg\Migration\Migration;

class ExecuteMigration
{

    protected $migration;

    public function __construct(Migration $migration)
    {
        $this->migration = $migration;
    }

    public function execute()
    {
        dd($this->migration);
    }

}