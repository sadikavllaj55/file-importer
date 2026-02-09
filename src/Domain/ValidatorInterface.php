<?php

namespace App\Domain;

interface ValidatorInterface
{
    public function validate(array $data): void;
}