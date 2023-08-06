<?php

declare(strict_types=1);

namespace App;

use App\Helper\EventInfoProviderHelper;

class Session
{
    private EventInfoProviderHelper $eventInfoProvider;

    public function __construct()
    {
        $this->eventInfoProvider = new EventInfoProviderHelper();
        session_start();
    }

    public function setLocalSessionVariable(string $name, string $value): void
    {
        $methodName = $this->eventInfoProvider->getCurrentFunctionName();
        $className = $this->eventInfoProvider->getCurrentClassName();

        $_SESSION[$className][$methodName][$name] = $value;
    }

    public function setGlobalSessionVariable(string $name, string $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function getLocalSessionVariable(string $name): ?string
    {
        $methodName = $this->eventInfoProvider->getCurrentFunctionName();
        $className = $this->eventInfoProvider->getCurrentClassName();
        
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
        $methodName = $this->eventInfoProvider->getCurrentFunctionName();
        $className = $this->eventInfoProvider->getCurrentClassName();

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