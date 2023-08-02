<?php

declare(strict_types=1);

namespace App\Helper;

use PDO;

class DatabaseManagerHelper
{
    public function __construct(
        private PDO $database
    )
    {
    }

    public function prepareSelectData(array $columns): string
    {
        $aliasColumns = [];

        foreach ($columns as $alias => $columnsName) {
            if (!is_int($alias)) {
                $aliasColumns[] = '`' . $alias . '`' . 'AS' . '\'' . $columnsName . '\'';
            } else {
                $aliasColumns[] = '`' . $columnsName . '`';
            }
        }

        return implode(', ', $aliasColumns);
    }
    
    public function prepareInsertValues(array $data): string
    {
        $formattedData = $this->quoteAllValues($data);
        return implode(', ', $formattedData);
    }
    
    public function prepareInsertKeys(array $data): string
    {
        $formattedData = $this->backtickQuoteAllValues($data);
        return implode(', ', $formattedData);
    }

    private function backtickQuoteAllValues(array $data): array
    {
        return array_map(
            function($val) {
                return '`' . $val . '`';
            },
            $data
        );
    }

    public function prepareUpdateData(array $data): string
    {
        $formatedData = [];

        foreach ($data as $key => $value) {
            $formatedData[] = $key . ' = ' . $this->database->quote($value);
        }

        return implode(', ', $formatedData);
    }

    public function prepareWhereValue(mixed $data, string $operator): string|int
    {
        if ($operator === 'BETWEEN' && is_array($data)) {
            $formatedData = $this->quoteAllValues($data);
            return implode(
                " AND ",
                $formatedData
            );
        }

        if ($operator === 'LIKE') {
            return $this->database->quote('%' . $data . '%');
        }

        if (!is_int($data)) {
            return $this->database->quote($data);
        }

        return $data;
    }

    private function quoteAllValues(array $data): array
    {
        return array_map(
            function($val) {
                return $this->database->quote($val);
            },
            $data
        );
    }
}