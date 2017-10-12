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
	/**
	 * @var string
	 */
	protected $type = 'DECIMAL';

	/**
	 * @var array
	 */
	protected $length = [8, 2];

	/**
	 * @return string
	 */
	public function getTypeWithLength()
	{
		return $this->type . ($this->length ? '(' . implode(',', $this->length) . ')' : '');
	}
}