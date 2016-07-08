<?php namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

class Hidable
{

    protected $table;

    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->boolean('hidden');
    }

}