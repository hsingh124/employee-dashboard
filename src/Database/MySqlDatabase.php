<?php

namespace App\Database;

use mysqli;

/**
 * An implementation of DatabaseInterface using PHP's native mysqli extension.
 */
class MySQLDatabase implements DatabaseInterface
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $sql): mixed
    {
        return $this->db->query($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(string $sql): mixed
    {
        return $this->db->prepare($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function getError(): string
    {
        return $this->db->error;
    }
}
