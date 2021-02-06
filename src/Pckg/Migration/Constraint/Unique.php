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

    function getType()
    {
        return 'UNIQUE';
    }

    function getDropType()
    {
        return 'INDEX';
    }
}
