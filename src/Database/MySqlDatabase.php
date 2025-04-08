<?php

namespace App\Database;

use mysqli;

class MySQLDatabase implements DatabaseInterface
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function query(string $sql): mixed
    {
        return $this->db->query($sql);
    }

    public function prepare(string $sql): mixed
    {
        return $this->db->prepare($sql);
    }

    public function getError(): string
    {
        return $this->db->error;
    }
}
