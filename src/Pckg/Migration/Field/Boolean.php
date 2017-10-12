<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

/**
 * Class Boolean
 *
 * @package Pckg\Migration\Field
 */
class Boolean extends Field
{
	/**
	 * @var string
	 */
	protected $type = 'TINYINT';

	/**
	 * @var int
	 */
	protected $length = 1;
}