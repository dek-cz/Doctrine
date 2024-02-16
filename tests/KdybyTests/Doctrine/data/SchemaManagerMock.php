<?php

namespace KdybyTests\Doctrine;

use Doctrine;
use Tester\Assert;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class SchemaManagerMock extends Doctrine\DBAL\Schema\MySQLSchemaManager
{

    /**
     * @param string $table
     * @return \Doctrine\DBAL\Schema\Index[]
     */
    public function listTableIndexes($table)
    {
        var_dump($table);
        $tables = [
            'test_empty' => ['uniq_name_surname' => new Doctrine\DBAL\Schema\Index('uniq_name_surname', ['name', 'surname'], TRUE)],
        ];

        if (!isset($tables[$table])) {
            Assert::fail("Table `$table` not found.");
        }

        return $tables[$table];
    }

}
