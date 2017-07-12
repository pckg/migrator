<?php namespace Pckg\Migration\Field;

class Id extends Integer
{

    protected $nullable = false;

    protected $autoincrement = true;

    public function autoincrement($boolean)
    {
        $this->autoincrement = $boolean;

        return $this;
    }

    public function getSql()
    {
        return parent::getSql() . ($this->autoincrement ? ' AUTO_INCREMENT' : '');
    }

}