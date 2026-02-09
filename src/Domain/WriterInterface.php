<?php

namespace App\Domain;

interface WriterInterface
{
    public function begin(): void;

    public function write(array $data): void;

    public function commit(): void;

    public function rollback(): void;
}