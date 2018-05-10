<?php

namespace Pckg\Migration;

use Pckg\Database\Repository;
use Pckg\Migration\Command\ExecuteMigration;

/**
 * Class Migration
 *
 * @package Pckg\Migration
 */
class Migration
{

    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @var string
     */
    protected $repository = Repository::class;

    /**
     * @var bool
     */
    protected $fields = true;

    /**
     * @var bool
     */
    protected $indexes = false;

    /**
     * @var bool
     */
    protected $relations = true;

    /**
     * @return $this
     */
    public function onlyFields()
    {
        $this->fields = true;
        $this->relations = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function onlyIndexes()
    {
        $this->indexes = true;

        return $this;
    }

    /**
     * @param      $table
     * @param bool $id
     * @param bool $primary
     *
     * @return Table
     */
    public function table($table, $id = true, $primary = true)
    {
        $table = $this->getTable($table);

        $this->tables[] = $table;

        if ($id) {
            $table->id('id', $primary);
        }

        return $table;
    }

    /**
     * @param $table
     *
     * @return Table
     */
    public function getTable($table)
    {
        $table = new Table($table);

        return $table;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param $repository
     *
     * @return $this
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return $this
     */
    public function up()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function dependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function partials()
    {
        return [];
    }

    /**
     * @return $this
     */
    public function afterFirstUp()
    {
        return $this;
    }

    public function shouldSkip($repository)
    {
        if ($repository) {
            if ($repository == 'default' && $this->getRepository() == Repository::class) {
                // ok
            } elseif (strpos($this->getRepository(), $repository) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     */
    public function save()
    {
        $executeMigration = (new ExecuteMigration($this));

        if (!$this->relations) {
            $executeMigration->onlyFields();
        }

        if ($this->indexes) {
            $executeMigration->onlyIndexes();
        }

        $executeMigration->execute();

        $this->tables = [];
    }

    /**
     * @param        $table
     * @param string $suffix
     *
     * @return Table
     */
    public function translatable($table, $suffix = '_i18n')
    {
        $translatable = new Table($table . $suffix);
        $this->tables[] = $translatable;

        $translatable->id('id', false)->references($table)->required();
        $translatable->varchar('language_id', 2)->references('languages', 'slug')->required();

        $translatable->primary('id', 'language_id');

        return $translatable;
    }

    public function languagable($table, $suffix = '_l11e')
    {
        $languagable = new Table($table . $suffix);
        $this->tables[] = $languagable;

        $languagable->id('id', false)->references($table)->required();
        $languagable->varchar('language_id', 2)->references('languages', 'slug')->required();

        $languagable->primary('id', 'language_id');

        return $languagable;
    }

    /**
     * @param        $table
     * @param string $suffix
     *
     * @return Table
     */
    public function permissiontable($table, $suffix = '_p17n')
    {
        $permissiontable = new Table($table . $suffix);
        $this->tables[] = $permissiontable;

        $permissiontable->id('id', false)->references($table, 'id', false);
        $permissiontable->integer('user_group_id')->references('user_groups');
        $permissiontable->varchar('action', 32)->required();

        /**
         * @T00D00 - add double index
         */

        return $permissiontable;
    }

    /**
     * @param        $table
     * @param        $morph
     * @param string $suffix
     *
     * @return Table
     */
    public function morphtable($table, $morph, $suffix = '_morphs')
    {
        $morphtable = new Table($table . $suffix);
        $this->tables[] = $morphtable;

        $morphtable->id('id');
        $morphtable->integer($morph)->references($table);
        $morphtable->varchar('morph_id');
        $morphtable->varchar('poly_id');

        return $morphtable;
    }

    /**
     * @param $msg
     */
    public function output($msg)
    {
        echo $msg . "\n";
    }
}