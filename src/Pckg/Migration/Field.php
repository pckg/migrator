<?php

namespace Pckg\Migration;

use Pckg\Migration\Constraint\Index;
use Pckg\Migration\Constraint\Primary;
use Pckg\Migration\Constraint\Unique;
use Pckg\Migration\Field\Id;

/**
 * Class Field
 *
 * @package Pckg\Migration
 */
class Field
{

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $type;

    /**
     * @var bool
     */
    protected $nullable = true;

    /**
     * @var null
     */
    protected $default = null;

    /**
     * @var
     */
    protected $length;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var bool
     */
    protected $drop = false;

    /**
     * Field constructor.
     *
     * @param Table $table
     * @param       $name
     */
    public function __construct(Table $table, $name)
    {
        $this->table = $table;
        $this->name = $name;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeWithLength()
    {
        return $this->type
            . ($this->length ? '(' . (is_array($this->length) ? implode(',', $this->length) : $this->length) . ')' : '');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return !$this->nullable;
    }

    /**
     * @param bool $nullable
     *
     * @return $this
     */
    public function nullable($nullable = true)
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function required($required = true)
    {
        $this->nullable = !$required;

        return $this;
    }

    /**
     * @param $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @param $length
     *
     * @return $this
     */
    public function length($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return $this
     */
    public function index()
    {
        $index = new Index($this->table, $this->name);

        $this->table->addConstraint($index);

        return $this;
    }

    /**
     * @return $this
     */
    public function primary()
    {
        $primary = new Primary($this->table, $this->name);

        $this->table->addConstraint($primary);

        return $this;
    }

    /**
     * @return $this
     */
    public function unique()
    {
        $unique = new Unique($this->table, $this->name);

        $this->table->addConstraint($unique);

        return $this;
    }

    /**
     * @param        $table
     * @param string $on
     *
     * @return $this
     */
    public function references($table, $on = 'id', $index = true)
    {
        $relation = new Relation($this, $table, $on);

        $this->table->addRelation($relation);

        if ($index) {
            $this->index();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function drop($drop = true)
    {
        $this->drop = true;

        return $this;
    }

    public function isDropped()
    {
        return $this->drop;
    }
}
