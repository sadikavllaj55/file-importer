<?php

namespace App\Domain;

final class ImportReport
{
    public int $processed = 0;
    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];
}