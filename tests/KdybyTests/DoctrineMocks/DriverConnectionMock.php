<?php
/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace KdybyTests\DoctrineMocks;

use Doctrine;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;

class DriverConnectionMock implements Doctrine\DBAL\Driver\Connection
{

    public function prepare(string $sql): Statement
    {
        return new StatementMock;
    }

    public function query(string $sql): Result
    {
        return new DriverResultMock;
    }

    public function quote($input, $type = \PDO::PARAM_STR)
    {
        
    }

    public function exec(string $sql): int
    {
        return 0;
    }

    public function lastInsertId($name = NULL)
    {
        
    }

    public function beginTransaction()
    {
        
    }

    public function commit()
    {
        
    }

    public function rollBack()
    {
        
    }

    public function errorCode()
    {
        
    }

    public function errorInfo()
    {
        
    }

}
