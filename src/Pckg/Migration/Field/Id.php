<?php namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

class Id extends Integer
{

    protected $nullable = false;

    public function getSql()
    {
        return parent::getSql() . ' AUTO_INCREMENT';
    }

}