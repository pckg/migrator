<?php

namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

/**
 * Class Timeable
 *
 * @package Pckg\Migration\Field\Group
 */
class Timeable
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * Timeable constructor.
     *
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->datetime('created_at')->setDefault('CURRENT_TIMESTAMP');
        $table->datetime('updated_at');
        $table->datetime('deleted_at');
    }
}