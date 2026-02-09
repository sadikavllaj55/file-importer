<?php

namespace App\Adapters\Reader;

use App\Domain\ReaderInterface;
use RuntimeException;

final class ReaderFactory
{
    private static array $readers = [
        'xlsx' => ExcelReader::class,
        'csv'  => CsvReader::class,
    ];

    public static function fromFile(string $filePath): ReaderInterface
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!isset(self::$readers[$ext])) {
            throw new RuntimeException("Unsupported file type: .$ext");
        }

        $readerClass = self::$readers[$ext];

        return new $readerClass($filePath);
    }
}
