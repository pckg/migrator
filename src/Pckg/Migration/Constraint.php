<?php

namespace Pckg\Migration;

/**
 * Class Constraint
 *
 * @package Pckg\Migration
 */
class Constraint
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * Constraint constructor.
     *
     * @param Table $table
     * @param array ...$fields
     */
    public function __construct(Table $table, ...$fields)
    {
        $this->table = $table;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->type . ' `' . $this->getName() . '` (`' . implode('`,`', $this->fields) . '`)';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return str_replace(' KEY', '', $this->type) . '__' . $this->table->getName() . '__' . implode('_',
                                                                                                      $this->fields);
    }
}