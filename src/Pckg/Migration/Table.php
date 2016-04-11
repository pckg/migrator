<?php namespace Pckg\Migration;

class Table
{

    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

}