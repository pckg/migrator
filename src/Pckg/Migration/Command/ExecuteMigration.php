<?php

namespace Pckg\Migration\Command;

use Exception;
use Pckg\Database\Entity;
use Pckg\Database\Helper\Cache;
use Pckg\Migration\Console\InstallMigrator;
use Pckg\Migration\Constraint\Constraint;
use Pckg\Migration\Field;
use Pckg\Migration\Migration;
use Pckg\Migration\Relation;
use Pckg\Migration\Table;
use Pckg\Migration\View;

/**
 * Class ExecuteMigration
 *
 * @package Pckg\Migration\Command
 */
class ExecuteMigration
{

    /**
     * @var Migration
     */
    protected $migration;

    /**
     * @var array
     */
    protected $sql = [];

    /**
     * @var array
     */
    protected $sqls = [];

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
     * ExecuteMigration constructor.
     *
     * @param Migration $migration
     */
    public function __construct(Migration $migration)
    {
        $this->migration = $migration;
    }

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
     *
     */
    public function execute()
    {
        $entity = new Entity();
        $entity->setRepository(context()->get($this->migration->getRepository()));
        $cache = $entity->getRepository()->getCache();

        foreach ($this->migration->getTables() as $table) {
            $this->sql = [];
            if ($cache->hasTable($table->getName())) {
                $this->updateTable($cache, $table);
            } else {
                $this->installTable($cache, $table);
            }
        }

        /*foreach ($this->migration->getViews() as $view) {
            if ($cache->hasTable($view->getName())) {
                $this->updateView($cache, $view);
            } else {
                $this->installView($cache, $view);
            }
        }*/

        if ($this->sqls) {
            $this->applyMigration();
        }
    }

    /**
     *
     */
    protected function applyMigration()
    {
        $sqls = implode(";\n\n", $this->sqls);
        $sqls = str_replace('`', '"', $sqls);
        $installMigrator = context()->getOrDefault(InstallMigrator::class);

        if (!$installMigrator) {
            echo $sqls;

            return;
        }

        $installMigrator->output($sqls);
        $message = 'Should I execute SQL statements on ' . $this->migration->getRepository() . '?';
        if ($installMigrator->option('yes') || $installMigrator->askConfirmation($message)) {
            $this->executeInRepository();
        }
    }

    /**
     * @throws Exception
     */
    protected function executeInRepository()
    {
        $repositoryName = $this->migration->getRepository();
        foreach ($this->sqls as $sql) {
            $sql = str_replace('`', '"', $sql);
            $repository = context()->get($repositoryName);

            $prepare = $repository->getConnection()->prepare($sql);
            $execute = $prepare->execute();
            if (!$execute) {
                throw new Exception('Cannot execute query! ' . "\n" . $sql . "\n" . 'Error code ' .
                                    $prepare->errorCode() . "\n" . $prepare->errorInfo()[2]);
            }
        }
    }

    public function updateView(Cache $cache, View $view)
    {
        $this->sql[] = 'DROP VIEW ' . $view->getName();
        $this->installView($cache, $view);
    }

    public function installView(Cache $cache, View $view)
    {
        $this->sql[] = 'CREATE VIEW ' . $view->getName() . ' AS ' . $view->buildSql();
    }

    /**
     * @param Cache $cache
     * @param Table $table
     */
    protected function updateTable(Cache $cache, Table $table)
    {
        foreach ($table->getFields() as $field) {
            $isDropped = $field->isDropped();
            if ($cache->tableHasField($table->getName(), $field->getName())) {
                if ($isDropped) {
                    $this->sql[] = 'DROP `' . $field->getName() . '`';
                    continue;
                }
                $sql = $this->updateField($cache, $table, $field);
                if ($sql) {
                    $this->sql[] = 'CHANGE `' . $field->getName() . '` ' . $sql;
                }
                continue;
            }

            if ($isDropped) {
                continue;
            }

            $sql = $this->installField($field);
            $this->sql[] = 'ADD ' . $sql;
            if (strpos($sql, 'AUTO_INCREMENT')) {
                $this->sql[] = 'ADD PRIMARY KEY(`' . $field->getName() . '`)';
            }
        }

        if ($this->sql) {
            $this->sqls[] = 'ALTER TABLE `' . $table->getName() . '` ' . "\n" . ' ' . implode(",\n ", $this->sql);
            $this->sql = [];
        }

        if ($this->indexes) {
            $tableConstraints = $cache->getTableConstraints($table->getName());
            foreach ($table->getConstraints() as $constraint) {
                if ($cache->tableHasConstraint($table->getName(), $constraint->getName())) {
                    $this->updateConstraint($cache, $table, $constraint);
                } else {
                    $found = false;
                    foreach (array_keys($tableConstraints) as $constraintName) {
                        if ($constraintName == 'PRIMARY' && $constraint->getFields(',') == 'id') {
                            $found = $constraintName;
                            break;
                        }
                        if ($constraintName == $constraint->getFields('_')) {
                            $found = $constraintName;
                            break;
                        }
                        if (strpos($constraintName, '__' . $constraint->getFields('_')) === false) {
                            continue;
                        }
                        $found = $constraintName;
                        break;
                    }
                    if (!$found) {
                        $this->installConstraint($constraint);
                    }
                }
            }
        }

        if ($this->sql) {
            $this->sqls[] = 'ALTER TABLE `' . $table->getName() . '` ' . "\n" . ' ' . implode(",\n ", $this->sql);
            $this->sql = [];
        }

        if ($this->relations) {
            foreach ($table->getRelations() as $relation) {
                $relationName = $relation->getName();

                if (strpos($relationName, 'FOREIGN_') !== 0) {
                    continue;
                }

                if ($cache->tableHasConstraint($table->getName(), $relation->getName())) {
                    $this->updateRelation($cache, $table, $relation);
                } else {
                    $this->installRelation($table, $relation);
                }
            }
        }
    }

    /**
     * @param Cache    $cache
     * @param Table    $table
     * @param Relation $relation
     */
    public function updateRelation(Cache $cache, Table $table, Relation $relation)
    {
        $cached = $cache->getConstraint($relation->getName(), $table->getName());

        if (!isset($cached['primary'])) {
            d("primary not set", $cached, $table->getName(), $relation->getName());

            return;
        }

        $current = $relation->getSqlByParams(
            $cached['primary'],
            $cached['references'],
            $cached['on'],
            Relation::RESTRICT,
            Relation::CASCADE
        );

        if ($current != $relation->getSql()) {
            $this->output('APPLY RELATION MANUALLY: ' . "\n" . $relation->getSql());
        }
    }

    /**
     * @param Table    $table
     * @param Relation $relation
     */
    public function installRelation(Table $table, Relation $relation)
    {
        $this->sqls[] = 'SET foreign_key_checks = 0';
        $this->sqls[] = 'ALTER TABLE `' . $table->getName() . '` ADD ' . $relation->getSql();
        $this->sqls[] = 'SET foreign_key_checks = 1';
    }

    /**
     * @param Cache $cache
     * @param Table $table
     * @param Field $field
     *
     * @return string
     */
    protected function updateField(Cache $cache, Table $table, Field $field)
    {
        //$this->output('Updating field ' . $table->getName() . '.' . $field->getName());
        $newSql = $field->getSql();
        $oldSql = $this->buildOldFieldSql($cache, $table, $field);

        if ($newSql != $oldSql) {
            return '`' . $field->getName() . '` ' . $newSql;
        }
    }

    /**
     * @param Cache      $cache
     * @param Table      $table
     * @param Constraint $key
     */
    protected function updateConstraint(Cache $cache, Table $table, Constraint $key)
    {
        /*
        $newSql = $key->getSql();
        $oldSql = $this->buildOldKeySql($cache, $table, $key);
        */
    }

    /**
     * @param Cache $cache
     * @param Table $table
     */
    protected function installTable(Cache $cache, Table $table)
    {
        //$this->output('Installing table ' . $table->getName());
        $primaryKey = null;
        foreach ($table->getFields() as $field) {
            $sql = $this->installField($field);
            $this->sql[] = $sql;
            if (strpos($sql, 'AUTO_INCREMENT')) {
                $primaryKey = $field->getName();
            }
        }

        if ($this->relations) {
            foreach ($table->getConstraints() as $constraint) {
                $this->installNewConstraint($constraint);
            }
        }

        if ($primaryKey && !in_array('PRIMARY KEY(`' . $primaryKey . '`)', $this->sql)) {
            $this->sql[] = 'PRIMARY KEY(`' . $primaryKey . '`)';
        }

        if ($this->sql) {
            $this->sqls[] = 'CREATE TABLE IF NOT EXISTS `' . $table->getName() . '` (' . "\n" .
                implode(",\n", $this->sql) . "\n" . ')';
            //implode(",\n", $this->sql) . "\n" . ') ENGINE=InnoDB DEFAULT CHARSET=utf8';
        }
    }

    /**
     * @param Field $field
     *
     * @return string
     */
    protected function installField(Field $field)
    {
        return '`' . $field->getName() . '` ' . $field->getSql();
    }

    /**
     * @param Constraint $key
     */
    protected function installNewConstraint(Constraint $key)
    {
        $this->sql[] = $key->getSql();
    }

    /**
     * @param Constraint $key
     */
    protected function installConstraint(Constraint $key)
    {
        $this->sql[] = 'ADD ' . $key->getSql();
    }

    /**
     * @param string $msg
     */
    protected function output($msg = '')
    {
        echo '<question>' . $msg . "</question>\n";
    }

    /**
     * @param Cache $cache
     * @param Table $table
     * @param Field $field
     *
     * @return string
     */
    protected function buildOldFieldSql(Cache $cache, Table $table, Field $field)
    {
        $cachedField = $cache->getField($field->getName(), $table->getName());

        return strtoupper($cachedField['type']) . ($cachedField['limit'] ? '(' . $cachedField['limit'] . ')' : '') .
            ($cachedField['null'] ? ' NULL' : ' NOT NULL') . ($cachedField['default']
                ? ' DEFAULT ' . ($cachedField['default'] == 'CURRENT_TIMESTAMP'
                    ? $cachedField['default'] : ("'" . $cachedField['default'] . "'")) : ($cachedField['null']
                    ? ' DEFAULT NULL' : '')) . ($cachedField['extra'] ? ' ' . strtoupper($cachedField['extra']) : '');
    }

    /**
     * @param Cache      $cache
     * @param Table      $table
     * @param Constraint $key
     *
     * @return string
     */
    protected function buildOldKeySql(Cache $cache, Table $table, Constraint $key)
    {
        $cachedConstraint = $cache->getConstraint($key->getName(), $table->getName());

        $type = $cachedConstraint['type'] == 'PRIMARY KEY' ? 'PRIMARY' : ($cachedConstraint['type'] . ' KEY');

        return $type . ' ' . $key->getName();
    }
}
