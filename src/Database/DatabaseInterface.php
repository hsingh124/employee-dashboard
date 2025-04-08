<?php

namespace App\Database;

interface DatabaseInterface
{
    public function query(string $sql): mixed;
    public function prepare(string $sql): mixed;
    public function getError(): string;
}