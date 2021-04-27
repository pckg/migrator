<?php

class PostgreSQLParserTestCest
{

    use \Pckg\Framework\Test\MockFramework;

    public function _before(UnitTester $I)
    {
        $context = $this->mockFramework();
        $context->bind(\Pckg\Framework\Application::class, new \Pckg\Framework\Application\Console(new \Pckg\Framework\Provider()));
    }

    // tests
    public function atryToTestCreateTable(UnitTester $I)
    {
        $migration = new Pckg\Migration\Migration();
        $mysqlDriver = new \Pckg\Database\Driver\PostgreSQL();
        $executeMigration = (new \Pckg\Migration\Command\ExecuteMigration($migration));
        $migration->setDriver($mysqlDriver);

        $myTable = $migration->table('my_table');
        $myTable->json('test_json');
        $myTable->integer('test_integer');
        $myTable->varchar('test_varchar');
        $myTable->longtext('test_longtext');
        $myTable->datetime('test_datetime');
        $myTable->boolean('test_boolean');
        $myTable->decimal('my_decimal');

        $executeMigration->installTable(new \Pckg\Database\Helper\Cache(new \Pckg\Database\Repository\Blank()), $myTable);

        $I->assertEquals([
            'CREATE TABLE IF NOT EXISTS "my_table" (
"id" SERIAL PRIMARY KEY,
"test_json" JSON NULL DEFAULT NULL,
"test_integer" INT NULL DEFAULT NULL,
"test_varchar" VARCHAR NULL DEFAULT NULL,
"test_longtext" TEXT NULL DEFAULT NULL,
"test_datetime" TIMESTAMP NULL DEFAULT NULL,
"test_boolean" BOOLEAN NULL DEFAULT NULL,
"my_decimal" DECIMAL(8,2) NULL DEFAULT NULL,
PRIMARY KEY("id")
)'
        ], $executeMigration->getSqls());
    }
}
