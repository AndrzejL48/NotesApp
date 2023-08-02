<?php

declare(strict_types=1);

namespace App\Interface;

interface ValidatorInterface
{
    public function validateData(array $params): bool;
}