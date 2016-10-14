<?php namespace Pckg\Migration\Constraint;

use Pckg\Migration\Constraint;

class Index extends Constraint
{

    protected $type = 'KEY';

    public function getSql()
    {
        return $this->type . '(`' . implode('`,`', $this->fields) . '`)';
    }

    public function getName()
    {
        return 'INDEX';
    }

}