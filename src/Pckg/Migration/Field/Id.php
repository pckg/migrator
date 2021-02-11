<?php

namespace Pckg\Migration\Field;

/**
 * Class Id
 *
 * @package Pckg\Migration\Field
 */
class Id extends Integer
{

    /**
     * @var bool
     */
    protected $nullable = false;

    /**
     * @var bool
     */
    protected $autoincrement = true;

    /**
     * @param $boolean
     *
     * @return $this
     */
    public function autoincrement($boolean)
    {
        $this->autoincrement = $boolean;

        return $this;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return parent::getSql() . ($this->autoincrement ? ' SERIAL' : '');
        return parent::getSql() . ($this->autoincrement ? ' AUTO_INCREMENT' : '');
    }
}
