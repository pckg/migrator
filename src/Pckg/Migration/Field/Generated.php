<?php

namespace Pckg\Migration\Field;

use Pckg\Migration\Field;
use Pckg\Migration\Table;

/**
 * Class Json
 *
 * @package Pckg\Migration\Field
 */
class Generated extends Field
{

    /**
     * @var string
     */
    protected $type = 'GENERATED ALWAYS AS';

    /**
     * @var
     */
    protected $generatedAs;

    public function __construct(Table $table, $name, $type, $as)
    {
        parent::__construct($table, $name);

        $this->type = $type;
        $this->generatedAs = $as;
    }

    public function getSql()
    {
        $sql = [$this->getTypeWithLength(), 'GENERATED ALWAYS AS (' . $this->generatedAs . ')'];

        return implode(' ', $sql);
    }
}
