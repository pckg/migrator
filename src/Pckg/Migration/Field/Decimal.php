<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

/**
 * Class Decimal
 *
 * @package Pckg\Migration\Field
 */
class Decimal extends Field
{
    use Unsigned;

    /**
     * @var string
     */
    protected $type = 'DECIMAL';

    /**
     * @var array
     */
    protected $length = [8, 2];
}
