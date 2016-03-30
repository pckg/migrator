<?php

namespace Pckg\Migration;

use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{

    protected function translaTable($table)
    {
        $i18n = $this->table($table . '_i18n', ['id' => false, 'primary_key' => ['id', 'language_id']]);

        $i18n->addColumn('id', 'integer', array('null' => false));
        // $i18n->addForeignKey('id', $table, 'id', array('delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'));

        $i18n->addColumn('language_id', 'string', array('null' => false));
        // $i18n->addForeignKey('language_id', 'languages', 'id', array('delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'));

        return $i18n;
    }

    protected function mtmTable($leftTable, $rightTable, $leftKey, $rightKey)
    {
        $mtm = $this->table($leftTable . '_' . $rightTable);

        $mtm->addColumn($leftKey, 'integer');
        // $mtm->addForeignKey($leftKey, $leftTable);

        $mtm->addColumn($rightKey, 'integer');
        // $mtm->addForeignKey($rightKey, $rightTable);

        return $mtm;
    }

    protected function addDatetimes($table)
    {
        $table->addColumn('dt_added', 'datetime', array('null' => true, 'default' => 'CURRENT_TIMESTAMP'));
        $table->addColumn('dt_updated', 'datetime', array('null' => true));
        $table->addColumn('dt_deleted', 'datetime', array('null' => true));
    }

    protected function addParent($table)
    {
        $table->addColumn('parent_id', 'integer');
    }

    protected function addSlug($table)
    {
        $table->addColumn('slug', 'string');
        $table->addIndex('slug', array('unique' => true));
    }

    protected function addTitle($table)
    {
        $table->addColumn('title', 'string');
    }

}