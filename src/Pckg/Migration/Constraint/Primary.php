<?php

namespace Pckg\Migration\Constraint;

/**
 * Class Primary
 *
 * @package Pckg\Migration\Constraint
 */
class Primary extends Constraint
{
    /**
     * @var string
     */
    protected $type = 'PRIMARY KEY';

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->type . '(`' . implode('`,`', $this->fields) . '`)';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'PRIMARY';
    }
}
