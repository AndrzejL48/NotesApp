<?php

declare(strict_types=1);

namespace App;

class Session
{
    public function __construct()
    {
        session_start();
    }

    public function setLocalSessionVariable(string $name, string $value): void
    {
        $methodName = thisElementInfo('method');
        $className = thisElementInfo('class');

        $_SESSION[$className][$methodName][$name] = $value;
    }

    public function setGlobalSessionVariable(string $name, string $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function getLocalSessionVariable(string $name): ?string
    {
        $methodName = thisElementInfo('method');
        $className = thisElementInfo('class');
        
        if ($this->isLocalVariableSet($name)) {
            return $_SESSION[$className][$methodName][$name];
        }

        return null;
    }

    public function getGlobalSessionVariable(string $name): ?string
    {
        if ($this->isLocalVariableSet($name)) {
            return $_SESSION[$name];
        }

        return null;
    }

    public function isLocalVariableSet(string $name): bool
    {
        $methodName = thisElementInfo('method');
        $className = thisElementInfo('class');

        return !empty($_SESSION[$className][$methodName][$name]);
    }

    public function isGlobalVariableSet(string $name): bool
    {
        return !empty($_SESSION[$name]);
    }

    public function killSession(): void
    {
        session_unset();
    }
}