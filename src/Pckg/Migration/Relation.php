<?php namespace Pckg\Migration;

class Relation
{

    const RESTRICT = 'RESTRICT';

    const CASCADE = 'CASCADE';

    const SET_NULL = 'SET NULL';

    const NO_ACTION = 'NO ACTION';

    protected $field;

    protected $onDelete = self::NO_ACTION;

    protected $onUpdate = self::CASCADE;

    public function __construct(Field $field, $references, $on)
    {
        $this->field = $field;
        $this->references = $references;
        $this->on = $on;
    }

    public function onDelete($action = self::NO_ACTION)
    {
        $this->onDelete = $action;

        return $this;
    }

    public function onUpdate($action = self::CASCADE)
    {
        $this->onUpdate = $action;

        return $this;
    }

}