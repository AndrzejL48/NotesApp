<?php

declare(strict_types=1);

namespace App\Validator;

use App\Validator\ValidatorManager;
use App\Interface\ValidatorInterface;

class AreValuesAllowedValidator
extends ValidatorManager
implements ValidatorInterface
{
    public function validateData(array $params): bool
    {
        $invalidElements = [];
        $isValid = true;

        foreach ($params as $checkValue => $compareValues) {
            if (!in_array($checkValue, $compareValues)) {
                $isValid = false;
                $invalidElements[] = $checkValue;
            }
        }

        if (!$isValid) {
            $this->setInvalidElements($invalidElements);
        }

        return $isValid;
    }
}