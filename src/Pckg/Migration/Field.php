<?php namespace Pckg\Migration;

use Pckg\Migration\Constraint\Index;
use Pckg\Migration\Constraint\Primary;
use Pckg\Migration\Constraint\Unique;

class Field
{

    protected $name;

    protected $type;

    protected $nullable = true;

    protected $default = null;

    protected $length;

    protected $table;

    public function __construct(Table $table, $name)
    {
        $this->table = $table;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTypeWithLength()
    {
        return $this->type . ($this->length ? '(' . $this->length . ')' : '');
    }

    public function getSql()
    {
        $sql = [];
        $sql[] = $this->getTypeWithLength();
        if ($this->isNullable()) {
            $sql[] = 'DEFAULT NULL';
        } else {
            $sql[] = 'NOT NULL';
        }

        return implode(' ', $sql);
    }

    public function isNullable()
    {
        return $this->nullable;
    }

    public function isRequired()
    {
        return !$this->nullable;
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
        $primary = new Primary($this->table, $this->name);

        $this->table->addConstraint($primary);

        return $this;
    }

    public function unique()
    {
        $unique = new Unique($this, $this->name);

        $this->table->addConstraint($unique);

        return $this;
    }

    public function references($table, $on = 'id')
    {
        $relation = new Relation($this, $table, $on);

        $this->table->addRelation($relation);

        return $relation;
    }

}