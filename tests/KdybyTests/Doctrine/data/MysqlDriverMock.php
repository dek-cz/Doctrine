<?php

namespace KdybyTests\Doctrine;

use Doctrine;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class MysqlDriverMock extends Doctrine\DBAL\Driver\PDO\MySQL\Driver
{

    public function createSchemaManager(Doctrine\DBAL\Connection $conn)
    {
        return new SchemaManagerMock($conn);
    }

}
