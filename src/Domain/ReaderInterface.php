<?php

namespace App\Domain;

interface ReaderInterface
{
    public function headers(): array;
    public function rows(): iterable;
}