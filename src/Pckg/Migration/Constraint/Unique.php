<?php

namespace Pckg\Migration\Constraint;

use Pckg\Migration\Constraint;

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
}