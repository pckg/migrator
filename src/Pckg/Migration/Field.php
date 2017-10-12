<?php

namespace Pckg\Migration;

use Pckg\Migration\Constraint\Index;
use Pckg\Migration\Constraint\Primary;
use Pckg\Migration\Constraint\Unique;

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
     * Field constructor.
     *
     * @param Table $table
     * @param       $name
     */
    public function __construct(Table $table, $name)
    {
        $this->table = $table;
        $this->name  = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTypeWithLength()
    {
        return $this->type . ($this->length ? '(' . $this->length . ')' : '');
    }

    /**
     * @return string
     */
    public function getSql()
    {
        $sql   = [];
        $sql[] = $this->getTypeWithLength();
        if ($this->isNullable()) {
            $sql[] = 'NULL';
        } else {
            $sql[] = 'NOT NULL';
        }

        if ($this->default) {
            $default = '';
            if ($this->default == 'CURRENT_TIMESTAMP') {
                $default = $this->default;
            } else {
                $default = "'" . $this->default . "'";
            }
            $sql[] = 'DEFAULT ' . $default;

        } elseif ($this->isNullable()) {
            $sql[] = 'DEFAULT NULL';
        }

        return implode(' ', $sql);
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
        $index = new Index($this, $this->name);

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
        $unique = new Unique($this, $this->name);

        $this->table->addConstraint($unique);

        return $this;
    }

    /**
     * @param        $table
     * @param string $on
     *
     * @return $this
     */
    public function references($table, $on = 'id')
    {
        $relation = new Relation($this, $table, $on);

        $this->table->addRelation($relation);

        return $this;
    }

    /**
     * @return $this
     */
    public function drop()
    {
        /**
         * @T00D00
         */
        return $this;
    }
}