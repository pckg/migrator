<?php namespace Pckg\Migration\Command;

use Pckg\Database\Entity;
use Pckg\Database\Helper\Cache;
use Pckg\Migration\Constraint;
use Pckg\Migration\Field;
use Pckg\Migration\Migration;
use Pckg\Migration\Table;

class ExecuteMigration
{

    protected $migration;

    protected $sql = [];
    protected $sqls = [];

    public function __construct(Migration $migration)
    {
        $this->migration = $migration;
    }

    public function execute()
    {
        //$this->output('Executing migration ' . get_class($this->migration));

        $entity = new Entity();
        $cache = $entity->getRepository()->getCache();
        foreach ($this->migration->getTables() as $table) {
            $this->sql = [];
            if ($cache->hasTable($table->getName())) {
                $this->updateTable($cache, $table);
            } else {
                $this->installTable($cache, $table);
            }
        }

        dd($this->sqls);
    }

    protected function updateTable(Cache $cache, Table $table)
    {
        //$this->output('Updating table ' . $table->getName());
        foreach ($table->getFields() as $field) {
            if ($cache->tableHasField($table->getName(), $field->getName())) {
                $this->updateField($cache, $table, $field);
            } else {
                $this->installField($cache, $table, $field);
            }
        }

        foreach ($table->getConstraints() as $constraint) {
            if ($cache->tableHasConstraint($table->getName(), $constraint->getName())) {
                $this->updateConstraint($cache, $table, $constraint);
            } else {
                $this->installConstraint($cache, $table, $constraint);
            }
        }

        if ($this->sql) {
            $this->sqls[] = 'ALTER TABLE `' . $table->getName() . '` ' . "\n"
                . implode(",\n", $this->sql);
        }
    }

    protected function updateField(Cache $cache, Table $table, Field $field)
    {
        //$this->output('Updating field ' . $table->getName() . '.' . $field->getName());
        $newSql = $field->getSql();
        $oldSql = $this->buildOldFieldSql($cache, $table, $field);

        if ($newSql != $oldSql) {
            $this->sql[] = 'CHANGE `' . $field->getName() . '` `' . $field->getName() . '` ' . $newSql;
        }
    }

    protected function updateConstraint(Cache $cache, Table $table, Constraint $key)
    {
        $newSql = $key->getSql();
        $oldSql = $this->buildOldKeySql($cache, $table, $key);
    }

    protected function installTable(Cache $cache, Table $table)
    {
        //$this->output('Installing table ' . $table->getName());
        foreach ($table->getFields() as $field) {
            $this->installField($cache, $table, $field);
        }

        foreach ($table->getConstraints() as $constraint) {
            $this->installNewConstraint($cache, $table, $constraint);
        }

        if ($this->sql) {
            $this->sqls[] = 'CREATE TABLE IF NOT EXISTS `' . $table->getName() . '` (' . "\n"
                . implode(",\n", $this->sql) . "\n" .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8';
        }
    }

    protected function installField(Cache $cache, Table $table, Field $field)
    {
        //$this->output('Installing field ' . $table->getName() . '.' . $field->getName());
        $this->sql[] = '`' . $field->getName() . '` ' . $field->getSql();
    }

    protected function installNewConstraint(Cache $cache, Table $table, Constraint $key)
    {
        //$this->output('Installing constraint ' . $table->getName() . '.' . $key->getName());
        $this->sql[] = $key->getSql();
    }

    protected function installConstraint(Cache $cache, Table $table, Constraint $key)
    {
        //$this->output('Installing constraint ' . $table->getName() . '.' . $key->getName());
        $this->sql[] = 'ADD ' . $key->getSql();
    }

    protected function output($msg)
    {
        echo $msg . "\n";
    }

    protected function buildOldFieldSql(Cache $cache, Table $table, Field $field)
    {
        $cachedField = $cache->getField($field->getName(), $table->getName());

        return strtoupper($cachedField['type'])
        . ($cachedField['limit'] ? '(' . $cachedField['limit'] . ')' : '')
        . ($cachedField['null'] == 'YES' ? ' DEFAULT NULL' : ' NOT NULL')
        . ($cachedField['extra'] ? ' ' . strtoupper($cachedField['extra']) : '');
    }

    protected function buildOldKeySql(Cache $cache, Table $table, Constraint $key)
    {
        $cachedConstraint = $cache->getConstraint($key->getName(), $table->getName());

        $type = $cachedConstraint['type'] == 'PRIMARY KEY'
            ? 'PRIMARY'
            : ($cachedConstraint['type'] . ' KEY');

        return $type . ' ' . $key->getName();
    }

}