<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;

/**
 * Class Text
 *
 * @package Pckg\Migration\Field
 */
class Text extends Field
{

    /**
     * @var string
     */
    protected $type = 'TEXT';

    protected $fulltext = false;

    public function fulltext($set = true)
    {
        $this->fulltext = $set;

        return $this;
    }
}
