<?php

namespace Pckg\Migration\Constraint;

/**
 * Class Unique
 *
 * @package Pckg\Migration\Constraint
 */
class Unique extends Constraint
{
    /**
     * @var string
     */
    protected $type = 'UNIQUE KEY';

    public function getType()
    {
        return 'UNIQUE';
    }

    public function getDropType()
    {
        return 'INDEX';
    }
}
