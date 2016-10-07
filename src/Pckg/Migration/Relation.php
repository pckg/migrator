<?php namespace Pckg\Migration;

class Relation
{

    const RESTRICT = 'RESTRICT';

    const CASCADE = 'CASCADE';

    const SET_NULL = 'SET NULL';

    const NO_ACTION = 'NO ACTION';

    protected $field;

    protected $references;

    protected $on;

    protected $onDelete = self::RESTRICT;

    protected $onUpdate = self::CASCADE;

    public function __construct(Field $field, $references, $on)
    {
        $this->field = $field;
        $this->references = $references;
        $this->on = $on;
    }

    public function getName()
    {
        $name = $this->field->getTable()->getName() . '__' . $this->field->getName();
        if (strlen($name) > 55) {
            $name = substr($name, -55);
        }

        return 'FOREIGN__' . $name;
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function getOn()
    {
        return $this->on;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getOnDelete()
    {
        return $this->onDelete;
    }

    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    public function onDelete($action = self::RESTRICT)
    {
        $this->onDelete = $action;

        return $this;
    }

    public function onUpdate($action = self::CASCADE)
    {
        $this->onUpdate = $action;

        return $this;
    }

    public function getSql()
    {
        return 'CONSTRAINT `' . $this->getName()
               . '` FOREIGN KEY (`' . $this->getField()->getName() . '`) ' .
               'REFERENCES `' . $this->getReferences() . '`(`' . $this->getOn() . '`) ' .
               'ON DELETE ' . $this->getOnDelete() . ' ' .
               'ON UPDATE ' . $this->getOnUpdate();
    }

    public function getSqlByParams($field, $references, $on, $onDelete, $onUpdate)
    {
        return 'CONSTRAINT `' . $this->getName()
               . '` FOREIGN KEY (`' . $field . '`) ' .
               'REFERENCES `' . $references . '`(`' . $on . '`) ' .
               'ON DELETE ' . $onDelete . ' ' .
               'ON UPDATE ' . $onUpdate;
    }

}