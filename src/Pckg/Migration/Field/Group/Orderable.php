<?php namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

class Orderable
{

    protected $table;

    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->integer('order');
    }

}