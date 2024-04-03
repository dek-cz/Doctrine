<?php
declare(strict_types = 1);

namespace Kdyby\Doctrine\Tracy;

interface DbalLogger extends \Doctrine\DBAL\Logging\SQLLogger
{

    public function connect(array $params): void;
    public function disconnect(): void;

}
