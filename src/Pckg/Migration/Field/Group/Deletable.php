<?php

namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

/**
 * Class Deletable
 *
 * @package Pckg\Migration\Field\Group
 */
class Deletable
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * Deletable constructor.
     *
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->datetime('deleted_at')->index();
    }
}
