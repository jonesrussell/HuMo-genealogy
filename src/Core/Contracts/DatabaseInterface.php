<?php

namespace HumoGen\Core\Contracts;

use PDO;
use PDOStatement;

/**
 * Database Interface
 * 
 * Defines the contract for database interactions.
 */
interface DatabaseInterface
{
    /**
     * Execute a query and return the statement.
     *
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return PDOStatement The executed statement
     * 
     * @throws \PDOException If the query fails
     */
    public function query(string $sql, array $params = []): PDOStatement;

    /**
     * Begin a transaction.
     *
     * @return bool True on success
     */
    public function beginTransaction(): bool;

    /**
     * Commit the active transaction.
     *
     * @return bool True on success
     */
    public function commit(): bool;

    /**
     * Rollback the active transaction.
     *
     * @return bool True on success
     */
    public function rollBack(): bool;

    /**
     * Get the ID of the last inserted row.
     *
     * @param string|null $name Name of the sequence object (if required)
     * @return string The last insert ID
     */
    public function lastInsertId(?string $name = null): string;

    /**
     * Get the underlying PDO instance.
     *
     * @return PDO The PDO instance
     */
    public function getPdo(): PDO;

    /**
     * Quote a string for use in a query.
     *
     * @param string $value The string to quote
     * @return string The quoted string
     */
    public function quote(string $value): string;

    /**
     * Execute a callback within a transaction.
     *
     * @param callable $callback The callback to execute
     * @return mixed The callback result
     * 
     * @throws \Throwable If the callback fails
     */
    public function transaction(callable $callback): mixed;
} 