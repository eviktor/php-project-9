<?php

namespace App\Repositories;

abstract class Repository
{
    public function __construct(protected \PDO $conn)
    {
    }
}
