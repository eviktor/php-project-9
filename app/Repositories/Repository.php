<?php

namespace App\Repositories;

use App\Models\Model;

abstract class Repository
{
    protected string $tableName = '';

    abstract protected function makeModelFromArray(array $modelData): Model;

    abstract protected function create(mixed $model): void;
    abstract protected function update(mixed $model): void;

    public function __construct(protected \PDO $conn)
    {
    }

    public function getEntities(string $order = ''): array
    {
        $sql = "SELECT * FROM $this->tableName" . ($order !== '' ? " ORDER BY $order" : '');
        $stmt = $this->conn->query($sql);

        if ($stmt === false) {
            throw new \Exception('Query failed: ' . implode(', ', $this->conn->errorInfo()));
        }

        return $this->fetchRecords($stmt);
    }

    protected function fetchRecords(\PDOStatement $stmt): array
    {
        $records = [];
        while ($row = $stmt->fetch()) {
            $record = $this->makeModelFromArray($row);
            $record->setId($row['id']);
            $records[] = $record;
        }
        return $records;
    }

    public function find(int $id): ?Model
    {
        $sql = "SELECT * FROM urls WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $rec = $this->makeModelFromArray($row);
            $rec->setId($row['id']);
            return $rec;
        }

        return null;
    }

    public function save(Model $rec): void
    {
        if ($rec->exists()) {
            $this->update($rec);
        } else {
            $this->create($rec);
        }
    }
}
