<?php

namespace App\Adapters\Reader;

use App\Domain\ReaderInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

final class ExcelReader implements ReaderInterface
{
    private array $headers = [];
    private array $rows = [];

    public function __construct(private string $filePath)
    {
        $this->validateFile();
        $this->loadFile();
    }

    private function validateFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException("File not found: {$this->filePath}");
        }

        $ext = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));
        if ($ext !== 'xlsx') {
            throw new RuntimeException('Only .xlsx files are supported.');
        }
    }

    private function loadFile(): void
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $allRows = $sheet->toArray(null, true, true, false);

        if (count($allRows) < 2) {
            throw new RuntimeException('Excel file contains no data.');
        }

        $this->headers = array_map('strtolower', array_shift($allRows));
        $this->rows = $allRows;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function rows(): iterable
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }
}
