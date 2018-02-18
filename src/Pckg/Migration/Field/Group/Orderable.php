<?php

namespace Pckg\Migration\Field\Group;

use Pckg\Migration\Table;

/**
 * Class Orderable
 *
 * @package Pckg\Migration\Field\Group
 */
class Orderable
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * Orderable constructor.
     *
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        $table->integer('order')->index();
    }
}