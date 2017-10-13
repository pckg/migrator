<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

/**
 * Class Varchar
 *
 * @package Pckg\Migration\Field
 */
class Varchar extends Field
{

    /**
     * @var string
     */
    protected $type = 'VARCHAR';

    /**
     * @var int
     */
    protected $length = 128;
}