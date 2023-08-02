<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOException;
use App\Exception\ConfigurationException;
use App\Exception\StorageException;
use App\Model\DatabaseManagerModel;

abstract class AbstractDatabaseModel
{
    private static array $configuration = [];

    private PDO $database;
    private array $config = [];
    private bool $isConnectionEstablished = false;
    protected ?string $tableName = null;
    protected DatabaseManagerModel $databaseManager;

    public function __construct()
    {
        if (!$this->isConnectionEstablished) {
            if (empty(self::$configuration['db'])) {
                throw new ConfigurationException("Database configuration error");
            }

            try {
                $this->config = self::$configuration['db'];
                $this->validateConfig();
                $this->createConnection();
            } catch(PDOException $e) {
                throw new StorageException('Database connection error');
            }
        }

        $this->databaseManager = new DatabaseManagerModel($this->database, $this->tableName);
    }

    public static function initConfiguration(array $configuration): void
    {
        self::$configuration = $configuration;
    }

    private function validateConfig(): void
    {
        $config = $this->config;
        $parmsKeys = [
            'database',
            'host',
            'user',
            'password'
        ];

        foreach ($parmsKeys as $key) {
            if (empty($config[$key])) {
                throw new ConfigurationException('Databse configuration params error');
            }
        }
    }

    private function createConnection(): void
    {
        $config = $this->config;

        $dsn = "mysql:dbname={$config['database']};host={$config['host']}";
        $this->database = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        $this->isConnectionEstablished = true;
    }
}