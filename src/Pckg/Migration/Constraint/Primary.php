<?php namespace Pckg\Migration\Constraint;

use Pckg\Migration\Constraint;

class Primary extends Constraint
{

    protected $type = 'PRIMARY KEY';

    public function getSql()
    {
        return $this->type . '(`' . implode('`,`', $this->fields) . '`)';
    }

    public function getName()
    {
        return 'PRIMARY';
    }

}