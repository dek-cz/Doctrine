<?php
/**
 * Test: Kdyby\Doctrine\Connection.
 *
 * @testCase Kdyby\Doctrine\ConnectionTest
 * @author Filip Procházka <filip@prochazka.su>
 * @package Kdyby\Doctrine
 */

namespace KdybyTests\Doctrine;

use Doctrine;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\DBAL\Driver\PDO\PDOException;
use Doctrine\DBAL\Query;
use Kdyby;
use KdybyTests\Doctrine\TestCase;
use KdybyTests\DoctrineMocks\ConnectionMock;
use KdybyTests\Doctrine\MysqlSchemaManagerFactoryMock;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class ConnectionTest extends TestCase
{

    /**
     * @return array
     */
    protected function loadMysqlConfig()
    {
        $configLoader = new Nette\DI\Config\Loader();
        $config = $configLoader->load(__DIR__ . '/../../mysql.neon');
        if (is_file(__DIR__ . '/../../mysql.local.neon')) {
            $config = Nette\DI\Config\Helpers::merge($configLoader->load(__DIR__ . '/../../mysql.local.neon'), $config);
        }

        return $config['doctrine'];
    }

    public function testPing()
    {
        $conn = Kdyby\Doctrine\Connection::create($this->loadMysqlConfig(), new Doctrine\DBAL\Configuration(), new Doctrine\Common\EventManager());

        /** @var \PDO $pdo */
        $pdo = $conn->getNativeConnection();
        $pdo->setAttribute(\PDO::ATTR_TIMEOUT, 3);
        $conn->query("SET interactive_timeout = 3");
        $conn->query("SET wait_timeout = 3");

        Assert::false($pdo instanceof Doctrine\DBAL\Driver\PingableConnection);

        $conn->connect();
        Assert::true($conn->ping());

        sleep(5);
        Assert::false($conn->ping());
    }

    /**
     * @dataProvider dataMySqlExceptions
     *
     * @param \Exception $exception
     * @param string $class
     * @param array $props
     */
    public function testDriverExceptions_MySQL($exception, $class, array $props)
    {
        $drv = new Driver();
        $schemaManagerFactory = new MysqlSchemaManagerFactoryMock();
        $config = new Configuration();
        $config->setSchemaManagerFactory($schemaManagerFactory);
        $conn = new ConnectionMock([], $drv, $config);
        $conn->setDatabasePlatform(new Doctrine\DBAL\Platforms\MySQLPlatform());
        $conn->throwOldKdybyExceptions = TRUE;

        $resolved = $conn->resolveException($exception);
        Assert::true($resolved instanceof $class);
        foreach ($props as $prop => $val) {
            Assert::same($val, $resolved->{$prop});
        }
    }

    /**
     * @return array
     */
    public function dataMySqlExceptions()
    {
        $e = new \PDOException('SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'name\' cannot be null', '23000');
        $e->errorInfo = ['23000', 1048, 'Column \'name\' cannot be null'];
        $emptyPdo = PDOException::new($e);

//        $driver = new MysqlDriverMock();
        $empty = new Doctrine\DBAL\Exception\DriverException($emptyPdo, new Query("INSERT INTO `test_empty` (`name`) VALUES (NULL)", [], []));
//        $empty = Doctrine\DBAL\Exception\DriverException::driverExceptionDuringQuery(
//                $driver, $emptyPdo, "INSERT INTO `test_empty` (`name`) VALUES (NULL)", []
//        );

        $e = new \PDOException('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'filip-prochazka\' for key \'uniq_name_surname\'', '23000');
        $e->errorInfo = ['23000', 1062, 'Duplicate entry \'filip-prochazka\' for key \'uniq_name_surname\''];
        $uniquePdo = PDOException::new($e);

        $unique = new Doctrine\DBAL\Exception\DriverException($uniquePdo, new Query("INSERT INTO `test_empty` (`name`, `surname`) VALUES ('filip', 'prochazka')", [], []));
        $unique->getSQLState();
//        $unique = Doctrine\DBAL\DBALException::driverExceptionDuringQuery(
//                $driver, $uniquePdo, "INSERT INTO `test_empty` (`name`, `surname`) VALUES ('filip', 'prochazka')", []
//        );

        return [
            [$empty, \Kdyby\Doctrine\EmptyValueException::class, ['column' => 'name']],
            [$unique, \Kdyby\Doctrine\DuplicateEntryException::class, ['columns' => ['uniq_name_surname' => ['name', 'surname']]]],
        ];
    }

    public function testDatabasePlatform_types()
    {
        $conn = new Kdyby\Doctrine\Connection([
            'memory' => TRUE,
            ], new Doctrine\DBAL\Driver\PDO\SQLite\Driver());
        $conn->setSchemaTypes([
            'test' => 'test',
        ]);
        $conn->setDbalTypes([
            'test' => \KdybyTests\DoctrineMocks\TestTypeMock::class,
        ]);
        $platform = $conn->getDatabasePlatform();
        Assert::same('test', $platform->getDoctrineTypeMapping('test'));
    }

}

(new ConnectionTest())->run();
