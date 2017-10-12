<?php

namespace Pckg\Migration;

/**
 * Class Relation
 *
 * @package Pckg\Migration
 */
class Relation
{
    const RESTRICT  = 'RESTRICT';
    const CASCADE   = 'CASCADE';
    const SET_NULL  = 'SET NULL';
    const NO_ACTION = 'NO ACTION';

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var
     */
    protected $references;

    /**
     * @var
     */
    protected $on;

    /**
     * @var string
     */
    protected $onDelete = self::RESTRICT;

    /**
     * @var string
     */
    protected $onUpdate = self::CASCADE;

    /**
     * Relation constructor.
     *
     * @param Field $field
     * @param       $references
     * @param       $on
     */
    public function __construct(Field $field, $references, $on)
    {
        $this->field      = $field;
        $this->references = $references;
        $this->on         = $on;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = $this->field->getTable()->getName() . '__' . $this->field->getName();
        if (strlen($name) > 55) {
            $name = substr($name, -55);
        }

        return 'FOREIGN__' . $name;
    }

    /**
     * @return mixed
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @return mixed
     */
    public function getOn()
    {
        return $this->on;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }

    /**
     * @return string
     */
    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function onDelete($action = self::RESTRICT)
    {
        $this->onDelete = $action;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function onUpdate($action = self::CASCADE)
    {
        $this->onUpdate = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return 'CONSTRAINT `' . $this->getName() . '` FOREIGN KEY (`' . $this->getField()->getName(
            ) . '`) ' . 'REFERENCES `' . $this->getReferences() . '`(`' . $this->getOn(
            ) . '`) ' . 'ON DELETE ' . $this->getOnDelete() . ' ' . 'ON UPDATE ' . $this->getOnUpdate();
    }

    /**
     * @param $field
     * @param $references
     * @param $on
     * @param $onDelete
     * @param $onUpdate
     *
     * @return string
     */
    public function getSqlByParams($field, $references, $on, $onDelete, $onUpdate)
    {
        return 'CONSTRAINT `' . $this->getName(
            ) . '` FOREIGN KEY (`' . $field . '`) ' . 'REFERENCES `' . $references . '`(`' . $on . '`) ' . 'ON DELETE ' . $onDelete . ' ' . 'ON UPDATE ' . $onUpdate;
    }
}