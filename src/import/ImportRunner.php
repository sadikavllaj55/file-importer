<?php

namespace App\import;

use App\Domain\ReaderInterface;
use App\Domain\MapperInterface;
use App\Domain\ValidatorInterface;
use App\Domain\WriterInterface;
use App\Domain\ImportReport;

final class ImportRunner
{
    public function __construct(
        private ReaderInterface $reader,
        private MapperInterface $mapper,
        private ValidatorInterface $validator,
        private WriterInterface $writer
    ) {}

    public function run(): ImportReport
    {
        $report = new ImportReport();

        $this->writer->begin();

        try {
            foreach ($this->reader->rows() as $rowNumber => $row) {
                $report->processed++;

                try {
                    $data = $this->mapper->map($row);
                    $this->validator->validate($data);
                    $this->writer->write($data);

                    $report->imported++;
                } catch (\Throwable $e) {
                    $report->skipped++;
                    $report->errors[] = [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $this->writer->commit();
        } catch (\Throwable $e) {
            $this->writer->rollback();
            throw $e;
        }

        return $report;
    }
}
