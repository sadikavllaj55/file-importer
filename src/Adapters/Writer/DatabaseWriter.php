<?php

namespace App\Adapters\Writer;

use App\Domain\WriterInterface;
use PDO;

final class DatabaseWriter implements WriterInterface
{
    private \PDOStatement $stmt;

    public function __construct(private PDO $pdo, array $columns)
    {
        $this->prepareStatement($columns);
    }

    private function prepareStatement(array $columns): void
    {
        $placeholders = array_map(fn ($c) => ':' . $c, $columns);
        $updateColumns = array_map(fn ($c) => "$c = VALUES($c)", $columns);

        $sql = sprintf(
            "INSERT INTO products (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
            implode(', ', $columns),
            implode(', ', $placeholders),
            implode(', ', $updateColumns)
        );

        $this->stmt = $this->pdo->prepare($sql);
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function write(array $data): void
    {
        $this->stmt->execute($data);
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}
