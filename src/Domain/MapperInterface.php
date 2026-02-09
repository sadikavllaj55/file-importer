<?php

namespace App\Domain;

interface MapperInterface
{
    public function map(array $row): array;
}