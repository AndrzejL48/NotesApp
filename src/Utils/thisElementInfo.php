<?php

declare(strict_types=1);

function thisElementInfo(string $requiredInfo)
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $key = array_key_last($trace) - 1;
    $currentElement = $trace[$key];

    switch ($requiredInfo) {
        case 'file':
            $currentElementDataType = $currentElement['file'];
            $startPosition = strrpos($currentElementDataType, '\\') + 1;
            $info = rtrim(substr($currentElementDataType, $startPosition), '.php'); 
            break;
        case 'line':
            $currentElementDataType = $currentElement['line'];
            $info = $currentElementDataType;
            break;
        case 'method':
            $currentElementDataType = $currentElement['function'];
            $info = $currentElementDataType;
            break;
        case 'class':
            $currentElementDataType = $currentElement['class'];
            $startPosition = strrpos($currentElementDataType, '\\') + 1;
            $info = substr($currentElementDataType, $startPosition);
            break;
    }

    return $info;
}