<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

use App\Adapters\Mapper\ProductMapper;
use App\Adapters\Mapper\TypeInferer;
use App\Adapters\Reader\ReaderFactory;
use App\Adapters\Validator\ProductValidator;
use App\Adapters\Writer\DatabaseWriter;
use App\import\ImportRunner;
use Database\Database;

$filePath = $argv[1] ?? null;

if (!$filePath) {
    fwrite(STDERR, "Usage: php bin/import-products.php <file>\n");
    exit(1);
}

$pdo = Database::connect();

$reader = ReaderFactory::fromFile($filePath);
$headers = ['gtin', 'language', 'title', 'picture', 'description', 'price', 'stock'];
$typeInferer = new TypeInferer();

$mapper    = new ProductMapper($headers, $reader, $typeInferer);
$validator = new ProductValidator();
$writer    = new DatabaseWriter($pdo, $headers);

$runner = new ImportRunner($reader, $mapper, $validator, $writer);
$report = $runner->run();

echo "Processed: {$report->processed}\n";
echo "Imported:  {$report->imported}\n";
echo "Skipped:   {$report->skipped}\n";

// 7. Exit
exit($report->skipped > 0 ? 2 : 0);
