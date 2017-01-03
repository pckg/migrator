<?php

namespace Pckg\Migration;

use Pckg\Database\Repository;
use Pckg\Migration\Command\ExecuteMigration;

class Migration
{

    protected $tables = [];

    protected $repository = Repository::class;

    public function table($table, $id = true, $primary = true)
    {
        $table = new Table($table);

        $this->tables[] = $table;

        if ($id) {
            $table->id('id', $primary);
        }

        return $table;
    }

    public function getTables()
    {
        return $this->tables;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function up()
    {
        return $this;
    }

    public function dependencies()
    {
        return [];
    }

    public function partials()
    {
        return [];
    }

    public function afterFirstUp()
    {
        return $this;
    }

    public function save()
    {
        (new ExecuteMigration($this))->execute();

        $this->tables = [];
    }

    public function translatable($table, $suffix = '_i18n')
    {
        $translatable = new Table($table . $suffix);
        $this->tables[] = $translatable;

        $translatable->id('id', false)->references($table)->required();
        $translatable->varchar('language_id', 2)->references('languages', 'slug')->required();

        $translatable->primary('id', 'language_id');

        return $translatable;
    }

    public function permissiontable($table, $suffix = '_p17n')
    {
        $permissiontable = new Table($table . $suffix);
        $this->tables[] = $permissiontable;

        $permissiontable->id('id', false)->references($table);
        $permissiontable->integer('user_group_id')->references('user_groups');
        $permissiontable->varchar('action', 32)->required();

        /**
         * @T00D00 - add double index
         */

        return $permissiontable;
    }

    public function morphtable($table, $morph, $suffix = '_morphs')
    {
        $morphtable = new Table($table . $suffix);
        $this->tables[] = $morphtable;

        $morphtable->id('id', false);
        $morphtable->integer($morph)->references($table);
        $morphtable->varchar('morph_id');
        $morphtable->varchar('poly_id');

        /**
         * @T00D00 - add double index
         */

        return $morphtable;
    }

}