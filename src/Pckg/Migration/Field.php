<?php namespace Pckg\Migration;

use Pckg\Migration\Key\Index;
use Pckg\Migration\Key\Primary;
use Pckg\Migration\Key\Unique;

class Field
{

    protected $name;

    protected $nullable = true;

    protected $default = null;

    protected $length;

    protected $table;

    public function __construct(Table $table, $name)
    {
        $this->table = $table;
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

    public function index()
    {
        $index = new Index($this, $this->name);

        $this->table->addConstraint($index);

        return $this;
    }

    public function primary()
    {
        $primary = new Primary($this, $this->name);

        $this->table->addConstraint($primary);

        return $this;
    }

    public function unique()
    {
        $unique = new Unique($this, $this->name);

        $this->table->addConstraint($unique);

        return $this;
    }

    public function references($table, $on)
    {
        $relation = new Relation($this, $table, $on);

        $this->table->addRelation($relation);

        return $relation;
    }

}