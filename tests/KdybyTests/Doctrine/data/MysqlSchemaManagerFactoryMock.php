<?php

namespace KdybyTests\Doctrine;

use Doctrine;

class MysqlSchemaManagerFactoryMock implements Doctrine\DBAL\Schema\SchemaManagerFactory
{

    public function createSchemaManager(Doctrine\DBAL\Connection $connection): \Doctrine\DBAL\Schema\AbstractSchemaManager
    {
        return new SchemaManagerMock($connection, $connection->getDatabasePlatform());
    }

}
