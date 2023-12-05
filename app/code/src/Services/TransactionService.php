<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\DBAL\Connection;

readonly class TransactionService
{
    public function __construct(
        private Connection $connection
    ) {}

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }
}
