<?php namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

class Decimal extends Field
{

    protected $type = 'DECIMAL';

    protected $length = [8, 2];

    public function getTypeWithLength()
    {
        return $this->type . ($this->length ? '(' . implode(',', $this->length) . ')' : '');
    }

}