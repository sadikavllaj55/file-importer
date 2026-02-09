<?php

namespace App\Adapters\Mapper;

use App\Domain\MapperInterface;
use App\Domain\ReaderInterface;

final class ProductMapper implements MapperInterface
{
    private array $transformations = [];

    public function __construct(
        private array $headers,
        private ReaderInterface $reader,
        private TypeInferer $inferer,
        private int $sampleSize = 5,
        private array $overrides = []
    ) {}

    private function initializeTransformations(): void
    {
        $rows = $this->reader->rows();
        $sampleRows = [];

        for ($i = 0; $i < $this->sampleSize && $rows->valid(); $i++) {
            $sampleRows[] = $rows->current();
            $rows->next();
        }

        foreach ($this->headers as $index => $header) {
            $column = strtolower(trim($header));
            $values = [];

            foreach ($sampleRows as $row) {
                if (!empty($row[$index])) {
                    $values[] = trim((string) $row[$index]);
                }
            }

            if (isset($this->overrides[$column])) {
                $type = strtolower($this->overrides[$column]);
                $this->transformations[$column] =
                    $this->inferer->transformerFor($type);
            } else {
                $this->transformations[$column] =
                    $this->inferer->inferTypeFromSamples($values);
            }
        }
    }

    private function ensureInitialized(): void
    {
        if ($this->transformations !== []) {
            return;
        }

        $this->initializeTransformations();
    }


    public function map(array $row): array
    {
        $this->ensureInitialized();
        $mapped = [];
        $headers = array_keys($this->transformations);

        foreach ($headers as $index => $column) {
            $value = $row[$index] ?? null;
            $mapped[':' . $column] = $this->transformations[$column]($value);
        }

        return $mapped;
    }
}
