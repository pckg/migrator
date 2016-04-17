<?php namespace Pckg\Migration;

class Field
{

    protected $name;

    protected $nullable = true;

    protected $default = null;

    protected $length;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function nullable($nullable = true)
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function required($required = true)
    {
        $this->nullable = !$required;

        return $this;
    }

    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    public function length($length)
    {
        $this->length = $length;

        return $this;
    }

}