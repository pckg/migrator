<?php namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

class Deletable
{

    protected $table;

    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->datetime('deleted_at');
    }

}