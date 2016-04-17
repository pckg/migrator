<?php namespace Pckg\Migration\Key;

use Pckg\Migration\Key;
use Pckg\Migration\Table;

class Index extends Key
{


    protected $table;

    protected $fields = [];

    public function __construct(Table $table, ...$fields)
    {
        $this->table = $table;
        $this->fields = $fields;
    }

}