<?php

namespace Pckg\Migration;

use Pckg\Migration\Command\ExecuteMigration;

class Migration
{

    protected $tables = [];

    public function table($table)
    {
        $table = new Table($table);

        $this->tables[] = $table;

        return $table;
    }

    public function save()
    {
        (new ExecuteMigration($this))->execute();
    }

    public function translatable($table, $suffix = '_i18n')
    {
        $translatable = new Table($table . $suffix);

        $translatable->id('id', false);
        $translatable->varchar('lang_id', 2)->references('languages', 'slug');

        $translatable->primary('id', 'lang_id');

        return $translatable;
    }

}