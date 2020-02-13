<?php

namespace Pckg\Migration;

/**
 * Class Constraint
 *
 * @package Pckg\Migration
 */
abstract class AbstractConstraint
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $fields = [];

    protected $type = 'CONSTRAINT';

    protected $name;

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
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name ?? str_replace([' KEY', 'CONSTRAINT'], ['', 'FOREIGN'], $this->type) . '__' . $this->table->getName() . '__' . $this->getFields('_');
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

    public function getDropType()
    {
        return $this->getType();
    }

    public function drop(Migration $migration)
    {
        $sql = 'ALTER TABLE `' . $this->table->getName() . '` DROP ' . $this->getDropType() . ' `' . $this->getName() . '`';

        $repository = context()->get($migration->getRepository());
        $prepare = $repository->getConnection()->prepare($sql);
        if (!$prepare) {
            throw new \Exception('Cannot prepare query ' . $sql);
        }
        $execute = $prepare->execute();
        if (!$execute) {
            throw new \Exception('Cannot execute query! ' . "\n" . $sql . "\n" . 'Error code ' .
                                $prepare->errorCode() . "\n" . $prepare->errorInfo()[2]);
        }
    }

}