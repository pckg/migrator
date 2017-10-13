<?php

namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

/**
 * Class Hidable
 *
 * @package Pckg\Migration\Field\Group
 */
class Hidable
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * Hidable constructor.
     *
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->boolean('hidden');
    }
}