<?php

declare(strict_types=1);

namespace App\Helper;

class EventInfoProviderHelper
{
    final public function getCurrentFileName(): ?string
    {
        $currentElement = $this->getCurrentElement(2);
        $info = null;

        if (!empty($currentElement['file'])) {
            $data = $currentElement['file'];
            $startPosition = strrpos($data, '\\') + 1;
            $info = substr($data, $startPosition);
        }

        return $info;
    }

    final public function getCurrentClassName(): ?string
    {
        $currentElement = $this->getCurrentElement();
        $info = null;

        if (!empty($currentElement['class'])) {
            $data = $currentElement['class'];
            $startPosition = strrpos($data, '\\') + 1;
            $info = substr($data, $startPosition);
        }

        return $info;
    }

    final public function getCurrentFunctionName(): ?string
    {
        $currentElement = $this->getCurrentElement();
        $data = $currentElement['function'] ?? null;

        return $data;
    }

    final public function getCurrentLine(): ?int
    {
        $currentElement = $this->getCurrentElement(2);
        $data = $currentElement['line'] ?? null;

        return $data;
    }

    private function getCurrentElement(int $keySubstractValue = 1): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $key = array_key_last($trace) - $keySubstractValue;

        return $trace[$key];
    }
}