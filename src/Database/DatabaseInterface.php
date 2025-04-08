<?php

namespace App\Database;

/**
 * Defines the minimal set of methods for interacting with a database.
 * Used to abstract the underlying DB engine (e.g., mysqli).
 */
interface DatabaseInterface
{
    /**
     * Executes a raw SQL query.
     * 
     * @param string $sql The SQL query to execute.
     * 
     * @return mixed The result of the query (e.g., mysqli_result).
     */
    public function query(string $sql): mixed;

    /**
     * Prepares an SQL statement for execution.
     * 
     * @param string $sql The SQL query to prepare.
     * 
     * @return mixed The prepared statement object.
     */
    public function prepare(string $sql): mixed;

    /**
     * Returns the last error message from the database.
     * 
     * @return string The error message.
     */
    public function getError(): string;
}