<?php

namespace Pckg\Migration;

/**
 * Class Constraint
 *
 * @package Pckg\Migration
 */
abstract class Constraint
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $fields = [];

    protected $type = 'INDEX';

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
        return str_replace([' KEY'], '', $this->type) . '__' . $this->table->getName() . '__' . $this->getFields('_');
    }

    public function getFields($separator = ',')
    {
        return implode($separator, $this->fields);
    }

    /**
     * @return string
     */
    function getType()
    {
        return $this->type;
    }

}