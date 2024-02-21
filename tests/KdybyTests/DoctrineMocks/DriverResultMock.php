<?php

namespace KdybyTests\DoctrineMocks;

use Doctrine;
use Doctrine\DBAL\Driver\Statement;

class DriverResultMock implements Doctrine\DBAL\Driver\Result
{

    public function columnCount(): int
    {
        return 0;
    }

    public function fetchAllAssociative(): array
    {
        return [];
    }

    public function fetchAllNumeric(): array
    {
        return [];
    }

    public function fetchAssociative(): array
    {
        return [];
    }

    public function fetchFirstColumn(): array
    {
        return [];
    }

    public function fetchNumeric()
    {
        
    }

    public function fetchOne()
    {
        
    }

    public function free(): void
    {
        
    }

    public function rowCount(): int
    {
        return 0;
    }

}
