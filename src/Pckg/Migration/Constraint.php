<?php namespace Pckg\Migration;

class Constraint
{

    /**
     * @var Table
     */
    protected $table;

    protected $fields = [];

    public function __construct(Table $table, ...$fields)
    {
        $this->table = $table;
        $this->fields = $fields;
    }

    public function getSql()
    {
        return $this->type . ' `' . $this->getName() . '` (`' . implode('`,`', $this->fields) . '`)';
    }

    public function getName()
    {
        return str_replace(' KEY', '', $this->type) . '__' . $this->table->getName() . '__' . implode('_', $this->fields);
    }

}