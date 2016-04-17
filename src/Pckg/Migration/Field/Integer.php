<?php namespace Pckg\Migration\Field;

use Pckg\Migration\Field;
use Pckg\Migration\Relation;

class Integer extends Field
{

    public function references($table, $on)
    {
        return new Relation($this, $table, $on);
    }

}