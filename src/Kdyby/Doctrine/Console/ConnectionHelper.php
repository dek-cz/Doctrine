<?php
declare(strict_types = 1);

namespace Kdyby\Doctrine\Console;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Nette\DI\Container as DIContainer;
use Symfony\Component\Console\Helper\Helper;

class ConnectionHelper extends Helper
{

    protected ConnectionProvider $provider;

    public function __construct(ConnectionProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Retrieves the Doctrine database Connection.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->provider->getDefaultConnection();
    }

    public function getConnectionByName(?string $name = null): Connection
    {
        return $name === null ? $this->provider->getDefaultConnection() : $this->provider->getConnection($name);
    }

    public function getProvider(): ConnectionProvider
    {
        return $this->provider;
    }

    /**
     * Returns the canonical name of this helper.
     */
    public function getName(): string
    {
        return 'connection';
    }

}
