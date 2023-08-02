<?php

declare(strict_types=1);

namespace App\Validator;

use App\Validator\ValidatorManager;
use App\Interface\ValidatorInterface;

class valueNotEmptyValidator
extends ValidatorManager
implements ValidatorInterface
{
    public function validateData(array $params): bool
    {
        $invalidElements = [];
        $isValid = true;

        foreach ($params as $name => $value) {
            if (empty($value)) {
                $isValid = false;
                $invalidElements[] = $name;
            }
        }

        if (!$isValid) {
            $this->setInvalidElements($invalidElements);
        }

        return $isValid;
    }
}