<?php

namespace App\Adapters\Mapper;

use DateTime;

final class TypeInferer
{
    public function detectType(string $value): string
    {
        if (preg_match('/^(true|false)$/i', $value)) return 'bool';
        if (preg_match('/^-?\d+$/', $value)) return 'int';
        if (preg_match('/^-?\d+\.\d+$/', $value)) return 'float';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return 'date';

        return 'string';
    }

    public function inferTypeFromSamples(array $values): callable
    {
        if (empty($values)) {
            return fn($v) => null;
        }

        $types = array_map(fn($v) => $this->detectType($v), $values);

        $safeType = $types[0];
        foreach ($types as $type) {
            if ($this->typeRank($type) > $this->typeRank($safeType)) {
                $safeType = $type;
            }
        }

        return $this->transformerFor($safeType);
    }

    private function typeRank(string $type): int
    {
        return match($type) {
            'bool'   => 1,
            'int'    => 2,
            'float'  => 3,
            'date'   => 4,
            'string' => 5,
            default  => 5,
        };
    }

    public function transformerFor(string $type): callable
    {
        return match ($type) {
            'bool'   => fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN),
            'int'    => fn($v) => (int)$v,
            'float'  => fn($v) => (float)$v,
            'date'   => fn($v) => new DateTime($v),
            default  => fn($v) => trim((string)$v),
        };
    }
}
