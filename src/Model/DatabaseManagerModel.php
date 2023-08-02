<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use App\Helper\DatabaseManagerHelper;

class DatabaseManagerModel
{
    const FETCH_ALL = 'all';
    const FETCH_COLUMN = 'column';
    const ASTERISK_WILDCARD = '*';
    const EQUAL_SIGN = '=';
    const LIKE_OPERATOR = 'LIKE';
    const BETWEEN_OPERATOR = 'BETWEEN';

    private PDO $database;
    private ?string $tableName = null;
    private array $query = [];
    private DatabaseManagerHelper $helper;

    public function __construct(PDO $database, string $tableName)
    {
        $this->database = $database;
        $this->tableName = $tableName;
        $this->helper = new DatabaseManagerHelper($database);
    }

    final public function select(?array $columns = null, ?string $tableName = null): void
    {
        $tableName = $tableName ?? $this->tableName;
        $fromattedData = !empty($columns) ? $this->helper->prepareSelectData($columns) : self::ASTERISK_WILDCARD;
        $this->query['CLAUSE'] = "SELECT $fromattedData FROM `$tableName`";
    }

    final public function insert(array $data): void
    {
        $tableName = $this->tableName;
        $formattedKeys = $this->helper->prepareInsertKeys(array_keys($data));
        $formattedValues = $this->helper->prepareInsertValues(array_values($data));
        $this->query['CLAUSE'] = "INSERT INTO `$tableName`($formattedKeys) VALUES($formattedValues)";
    }

    final public function update(array $data): void
    {
        $tableName = $this->tableName;
        $formattedData = $this->helper->prepareUpdateData($data);
        $this->query['CLAUSE'] = "UPDATE `$tableName` SET $formattedData";
    }

    final public function delete(): void
    {
        $tableName = $this->tableName;
        $this->query['CLAUSE'] = "DELETE FROM `$tableName`";
    }

    final public function count(string $column = self::ASTERISK_WILDCARD): void
    {
        $tableName = $this->tableName;
        $column = $column === self::ASTERISK_WILDCARD ? $column : '`' . $column . '`';
        $this->query['AGGREGATE'] = "SELECT COUNT($column) FROM `$tableName`";
    }

    final public function avg(string $column): void
    {
        $tableName = $this->tableName;
        $this->query['AGGREGATE'] = "SELECT AVG(`$column`) FROM `$tableName`";
    }

    final public function sum(string $column): void
    {
        $tableName = $this->tableName;
        $this->query['AGGREGATE'] = "SELECT SUM(`$column`) FROM `$tableName`";
    }

    final public function min(string $column): void
    {
        $tableName = $this->tableName;
        $this->query['AGGREGATE'] = "SELECT MIN(`$column`) FROM `$tableName`";
    }

    final public function max(string $column): void
    {
        $tableName = $this->tableName;
        $this->query['AGGREGATE'] = "SELECT MAX(`$column`) FROM `$tableName`";
    }

    final public function where(string $key, mixed $value, string $operator = self::EQUAL_SIGN): void
    {
        $value = $this->helper->prepareWhereValue($value, $operator);
        $this->query['CONDITION'][] = "WHERE `$key` $operator $value";
    }

    final public function andWhere(string $key, mixed $value, string $operator = self::EQUAL_SIGN): void
    {
        $value = $this->helper->prepareWhereValue($value, $operator);
        $this->query['CONDITION'][] = "AND `$key` $operator $value";
    }

    final public function orWhere(string $key, mixed $value, string $operator = self::EQUAL_SIGN): void
    {
        $value = $this->helper->prepareWhereValue($value, $operator);
        $this->query['CONDITION'][] = "OR `$key` $operator $value";
    }

    final public function group(string $column): void
    {
        $this->query['DIRECTIVE'][] = "GROUP BY `$column`";
    }

    final public function sort(string $column, string $order): void
    {
        $this->query['DIRECTIVE'][] = "ORDER BY `$column` $order";
    }

    final public function limit(int $number): void
    {
        $this->query['DIRECTIVE'][] = "LIMIT $number";
    }

    final public function offset(int $number): void
    {
        $this->query['DIRECTIVE'][] = "OFFSET $number";
    }

    final public function executeQuery(): bool
    {
        $query = $this->buildQuery();
        $this->database->exec($query);
        return true;
    }

    final public function fetch(?string $type = null)
    {
        $query = $this->buildQuery();
        $result = $this->database->query($query);

        return match($type) {
            'all' => $result->fetchAll(PDO::FETCH_ASSOC),
            'column' => $result->fetchColumn(),
            default => $result->fetch(PDO::FETCH_ASSOC)
        };
    }

    private function buildQuery(): string
    {
        $queryStructure = $this->query;
        $this->clearQueryContainer();

        foreach($queryStructure as $key => $query) {
            if (is_array($query)) {
                $queryStructure[$key] = implode(" ", $queryStructure[$key]);
            }
        }

        return implode(" ", $queryStructure);
    }

    private function clearQueryContainer(): void
    {
        unset($this->query);
    }
}