<?php

namespace Pckg\Migration;

use Pckg\Migration\Command\ExecuteMigration;

class Migration
{

    protected $tables = [];

    public function table($table, $id = true)
    {
        $table = new Table($table);

        $this->tables[] = $table;

        if ($id) {
            $table->id();
        }

        return $table;
    }

    public function getTables()
    {
        return $this->tables;
    }

    public function save()
    {
        (new ExecuteMigration($this))->execute();
    }

    public function translatable($table, $suffix = '_i18n')
    {
        $translatable = new Table($table . $suffix);
        $this->tables[] = $translatable;

        $translatable->id('id', false);
        $translatable->varchar('language_id', 2)->references('languages', 'slug')->required();

        $translatable->primary('id', 'language_id');

        return $translatable;
    }

    public function permissiontable($table, $suffix = '_p17n')
    {
        $permissiontable = new Table($table . $suffix);
        $this->tables[] = $permissiontable;

        $permissiontable->id('id', false);
        $permissiontable->integer('user_group_id')->references('user_groups');
        $permissiontable->varchar('action', 32);

        return $permissiontable;
    }

    public function morphtable($table, $morph, $suffix = '_morphs')
    {
        $morphtable = new Table($table . $suffix);
        $this->tables[] = $morphtable;

        $morphtable->id('id');
        $morphtable->integer($morph)->references($table);
        $morphtable->varchar('morph_id');
        $morphtable->integer('poly_id');

        return $morphtable;
    }

}