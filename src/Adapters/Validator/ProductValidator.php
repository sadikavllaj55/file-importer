<?php

namespace App\Adapters\Validator;

use App\Domain\ValidatorInterface;
use RuntimeException;

final class ProductValidator implements ValidatorInterface
{
    public function validate(array $data): void
    {
        if (empty(array_filter($data))) {
            throw new RuntimeException('Row is empty');
        }

        if ($data['price'] < 0) {
            throw new RuntimeException('Price must be positive');
        }
    }
}
