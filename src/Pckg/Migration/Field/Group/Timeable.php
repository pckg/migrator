<?php namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

class Timeable
{

    protected $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
        
        $table->datetime('created_at')->setDefault('NOW()');
        $table->datetime('updated_at');
        $table->datetime('deleted_at');
    }

}