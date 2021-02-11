<?php

namespace Pckg\Migration\Field;

trait Unsigned
{

    /**
     * @var bool
     */
    protected $unsigned = false;

    public function unsigned($unsigned = true)
    {
        $this->unsigned = $unsigned;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeWithLength()
    {
        if ($this->type === 'INT' || $this->type === 'VARCHAR') {
            return $this->type;
        }
        return $this->type . ($this->length ? '(' . $this->length . ')' : '') . ($this->unsigned ? ' UNSIGNED' : '');
    }
}
