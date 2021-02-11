<?php

use Pckg\Migration\Command\ExecuteMigration;

class MySQLParserTestCest
{

    use \Pckg\Framework\Test\MockFramework;

    public function _before(UnitTester $I)
    {
        $context = $this->mockFramework();
        $context->bind(\Pckg\Framework\Application::class, new \Pckg\Framework\Application\Console(new \Pckg\Framework\Provider()));
    }

    // tests
    public function tryToTestCreateTable(UnitTester $I)
    {
        $migration = new Pckg\Migration\Migration();
        $mysqlDriver = new \Pckg\Database\Driver\MySQL();
        $executeMigration = (new ExecuteMigration($migration));
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
            'CREATE TABLE IF NOT EXISTS `my_table` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`test_json` JSON NULL DEFAULT NULL,
`test_integer` INT(11) NULL DEFAULT NULL,
`test_varchar` VARCHAR(255) NULL DEFAULT NULL,
`test_longtext` LONGTEXT NULL DEFAULT NULL,
`test_datetime` DATETIME NULL DEFAULT NULL,
`test_boolean` TINYINT(1) NULL DEFAULT NULL,
`my_decimal` DECIMAL(8,2) NULL DEFAULT NULL,
PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        ], $executeMigration->getSqls());
    }
}
