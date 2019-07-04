<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

/**
 * Class Integer
 *
 * @package Pckg\Migration\Field
 */
class Integer extends Field
{

    use Unsigned;

    /**
     * @var string
     */
    protected $type = 'INT';

    /**
     * @var int
     */
    protected $length = 11;

}