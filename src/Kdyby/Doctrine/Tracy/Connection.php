<?php
declare(strict_types = 1);

namespace Kdyby\Doctrine\Tracy;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement as DriverStatement;
use Psr\Log\LoggerInterface;

final class Connection extends AbstractConnectionMiddleware
{

    private LoggerInterface $logger;

    /** @internal This connection can be only instantiated by its driver. */
    public function __construct(ConnectionInterface $connection, LoggerInterface $logger)
    {
        parent::__construct($connection);

        $this->logger = $logger;
    }

    public function __destruct()
    {
        $this->logger->disconnect();
    }

    public function prepare(string $sql): DriverStatement
    {
        return new Statement(
            parent::prepare($sql),
            $this->logger,
            $sql,
        );
    }

    public function query(string $sql): Result
    {
//        $this->logger->debug('Executing query: {sql}', ['sql' => $sql]);
        $this->logger->startQuery($sql);
        try {
            return parent::query($sql);
        } finally {
            $this->logger->stopQuery();
        }
    }

    public function exec(string $sql): int
    {
        $this->logger->debug('Executing statement: {sql}', ['sql' => $sql]);

        $this->logger->startQuery($sql);
        try {
            return parent::exec($sql);
        } finally {
            $this->logger->stopQuery();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        $this->logger->debug('Beginning transaction');

        $this->logger->startQuery('Beginning transaction');
        try {
            parent::beginTransaction();
        } finally {
            $this->logger->stopQuery();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        $this->logger->debug('Committing transaction');
        $this->logger->startQuery('Committing transaction');
        try {
            parent::commit();
        } finally {
            $this->logger->stopQuery();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
        $this->logger->debug('Rolling back transaction');

        $this->logger->startQuery('Rolling back transaction');
        try {
            parent::rollBack();
        } finally {
            $this->logger->stopQuery();
        }
    }

}
