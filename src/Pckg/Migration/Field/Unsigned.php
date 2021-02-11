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

    public function isUnsigned()
    {
        return $this->unsigned;
    }
}
