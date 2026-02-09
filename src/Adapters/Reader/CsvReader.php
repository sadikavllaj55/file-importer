<?php

namespace App\Adapters\Reader;

use App\Domain\ReaderInterface;
use RuntimeException;

final class CsvReader implements ReaderInterface
{
    private array $headers = [];

    public function __construct(
        private string $filePath,
        private string $delimiter = ',',
        private string $enclosure = '"',
        private string $escape = '\\'
    ) {
        $this->validateFile();
    }

    private function validateFile(): void
    {
        if (!is_file($this->filePath)) {
            throw new RuntimeException("CSV file not found: {$this->filePath}");
        }

        if (strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION)) !== 'csv') {
            throw new RuntimeException('Invalid CSV file extension.');
        }
    }

    public function headers(): array
    {
        if ($this->headers) {
            return $this->headers;
        }

        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            throw new RuntimeException('Unable to open CSV file.');
        }

        $row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape);
        fclose($handle);

        if (!$row) {
            throw new RuntimeException('CSV file is empty or missing header row.');
        }

        return $this->headers = array_map(
            static fn ($h) => strtolower(trim((string)$h)),
            $row
        );
    }

    public function rows(): iterable
    {
        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            throw new RuntimeException('Unable to open CSV file.');
        }

        // skip header
        fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape);

        while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {
            yield $row;
        }

        fclose($handle);
    }
}
