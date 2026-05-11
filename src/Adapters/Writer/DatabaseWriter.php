<?php

namespace App\Adapters\Writer;

use App\Domain\WriterInterface;
use PDO;

final class DatabaseWriter implements WriterInterface
{
    private array $buffer = [];
    private int $batchSize = 1000;
    private array $columns;
    private int $queryCount = 0;
    private float $totalTime = 0.0;

    public function __construct(private PDO $pdo, array $columns)
    {
        $this->columns = array_values($columns);
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function write(array $data): void
    {
        $this->buffer[] = $data;

        if (count($this->buffer) >= $this->batchSize) {
            $this->flush();
        }
    }

    private function flush(): void
    {
        if (empty($this->buffer)) {
            return;
        }

        $columns = $this->columns;

        $valuesPart = [];
        $bindings = [];

        foreach ($this->buffer as $row) {
            $placeholders = [];

            foreach ($columns as $col) {
                $placeholders[] = '?';
                $bindings[] = $row[$col] ?? null;
            }

            $valuesPart[] = '(' . implode(',', $placeholders) . ')';
        }

        $updatePart = implode(', ', array_map(
            fn($c) => "$c = VALUES($c)",
            $columns
        ));

        $sql = sprintf(
            "INSERT INTO products (%s) VALUES %s ON DUPLICATE KEY UPDATE %s",
            implode(', ', $columns),
            implode(', ', $valuesPart),
            $updatePart
        );

        $start = microtime(true);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        $end = microtime(true);

        $this->queryCount++;
        $this->totalTime += ($end - $start);

        $this->buffer = [];
    }
    public function commit(): void
    {
        $this->flush();
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->buffer = [];
        $this->pdo->rollBack();
    }

    public function getStats(): array
    {
        return [
            'queries' => $this->queryCount,
            'time_seconds' => $this->totalTime,
        ];
    }
}