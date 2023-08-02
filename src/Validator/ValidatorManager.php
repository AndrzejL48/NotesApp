<?php

declare(strict_types=1);

namespace App\Validator;

class ValidatorManager
{
    private array $invalidElements = [];
    private ValidatorManager $validatorObject;

    public function validate(string $validatorType = null, array $validationParams = []): bool
    {
        $validatorClassName = $validatorType . 'Validator';
        $validatorClassNamespace = 'App\Validator\\' . $validatorClassName;

        if (class_exists($validatorClassNamespace)) {
            $validatorClass = new $validatorClassNamespace();
            $this->setCurrentValidatorObject($validatorClass);
            return $validatorClass->validateData($validationParams);
        }
    }

    private function setCurrentValidatorObject(ValidatorManager $validatorObject): void
    {
        $this->validatorObject = $validatorObject;
    }

    protected function setInvalidElements(array $elements): void
    {
        $this->invalidElements = $elements;
    }

    public function getInvalidElements(bool $returnArray = true)
    {
        $invalidElements = $this->validatorObject->invalidElements;

        if ($returnArray) {
            return $invalidElements;
        }

        return implode(", ", $invalidElements);
    }
}
