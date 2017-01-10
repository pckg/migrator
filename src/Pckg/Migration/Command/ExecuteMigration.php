<?php namespace Pckg\Migration\Command;

use Exception;
use Pckg\Database\Entity;
use Pckg\Database\Helper\Cache;
use Pckg\Migration\Console\InstallMigrator;
use Pckg\Migration\Constraint;
use Pckg\Migration\Field;
use Pckg\Migration\Migration;
use Pckg\Migration\Relation;
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

        if ($this->sqls) {
            $this->applyMigration();
        }
    }

    protected function applyMigration()
    {
        $sqls = implode(";\n\n", $this->sqls);
        $installMigrator = context()->getOrDefault(InstallMigrator::class);

        if (!$installMigrator) {
            echo $sqls;

            return;
        }

        $installMigrator->output($sqls);
        $message = 'Should I execute SQL statements on ' . $this->migration->getRepository() . '?';
        if ($installMigrator->askConfirmation($message)) {
            $this->executeInRepository();
        }
    }

    protected function executeInRepository()
    {
        $repositoryName = $this->migration->getRepository();
        foreach ($this->sqls as $sql) {
            $repository = context()->get($repositoryName);

            $prepare = $repository->getConnection()->prepare($sql);
            $execute = $prepare->execute();
            if (!$execute) {
                throw new Exception(
                    'Cannot execute query! ' . "\n" . $sql . "\n" . 'Error code ' . $prepare->errorCode(
                    ) . "\n" . $prepare->errorInfo()[2]
                );
            }
        }
    }

    protected function updateTable(Cache $cache, Table $table)
    {
        foreach ($table->getFields() as $field) {
            if ($cache->tableHasField($table->getName(), $field->getName())) {
                $sql = $this->updateField($cache, $table, $field);
                if ($sql) {
                    $this->sql[] = 'CHANGE `' . $field->getName() . '` ' . $sql;
                }
            } else {
                $sql = $this->installField($cache, $table, $field);
                $this->sql[] = 'ADD ' . $sql;
            }
        }

        if ($this->sql) {
            $this->sqls[] = 'ALTER TABLE `' . $table->getName() . '` ' . "\n"
                            . ' ' . implode(",\n ", $this->sql);
        }

        foreach ($table->getConstraints() as $constraint) {
            if ($cache->tableHasConstraint($table->getName(), $constraint->getName())) {
                $this->updateConstraint($cache, $table, $constraint);
            } else {
                $this->installConstraint($cache, $table, $constraint);
            }
        }

        foreach ($table->getRelations() as $relation) {
            $relationName = $relation->getName();

            if (strpos($relationName, 'FOREIGN_') !== 0) {
                continue;
            }

            if ($cache->tableHasConstraint($table->getName(), $relation->getName())) {
                $this->updateRelation($cache, $table, $relation);
            } else {
                $this->installRelation($cache, $table, $relation);
            }
        }
    }

    public function updateRelation(Cache $cache, Table $table, Relation $relation)
    {
        $cached = $cache->getConstraint($relation->getName(), $table->getName());
        
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

    public function installRelation(Cache $cache, Table $table, Relation $relation)
    {
        $this->sqls[] = 'SET foreign_key_checks = 0';
        $this->sqls[] = 'ALTER TABLE `' . $table->getName() . '` ADD ' . $relation->getSql();
        $this->sqls[] = 'SET foreign_key_checks = 1';
    }

    protected function updateField(Cache $cache, Table $table, Field $field)
    {
        //$this->output('Updating field ' . $table->getName() . '.' . $field->getName());
        $newSql = $field->getSql();
        $oldSql = $this->buildOldFieldSql($cache, $table, $field);

        if ($newSql != $oldSql) {
            return '`' . $field->getName() . '` ' . $newSql;
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
            $this->sql[] = $this->installField($cache, $table, $field);
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
        return '`' . $field->getName() . '` ' . $field->getSql();
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

    protected function output($msg = '')
    {
        echo '<question>' . $msg . "</question>\n";
    }

    protected function buildOldFieldSql(Cache $cache, Table $table, Field $field)
    {
        $cachedField = $cache->getField($field->getName(), $table->getName());

        return strtoupper($cachedField['type'])
               . ($cachedField['limit'] ? '(' . $cachedField['limit'] . ')' : '')
               . ($cachedField['null'] ? ' NULL' : ' NOT NULL')
               . ($cachedField['default']
            ? ' DEFAULT ' . ($cachedField['default'] == 'CURRENT_TIMESTAMP'
                ? $cachedField['default']
                : ("'" . $cachedField['default'] . "'"))
            : ($cachedField['null'] ? ' DEFAULT NULL' : ''))
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