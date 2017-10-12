<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

/**
 * Class IdString
 *
 * @package Pckg\Migration\Field
 */
class IdString extends Varchar
{
    /**
     * @var bool
     */
    protected $nullable = false;
}