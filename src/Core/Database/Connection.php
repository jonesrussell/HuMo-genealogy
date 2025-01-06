<?php

namespace HumoGen\Core\Database;

use PDO;

class Connection
{
    protected PDO $pdo;

    public function __construct(
        string $host,
        string $database,
        string $username,
        string $password,
        string $port = '3306',
        string $charset = 'utf8mb4'
    ) {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        
        $this->pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Quote a string for use in a query.
     */
    public function quote(string $value): string
    {
        return $this->pdo->quote($value);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
} 