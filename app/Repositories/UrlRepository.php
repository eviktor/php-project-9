<?php

namespace App\Repositories;

use App\Models\Model;
use App\Models\Url;

class UrlRepository extends Repository
{
    protected string $tableName = 'urls';

    protected function makeModelFromArray(array $modelData): Model
    {
        return Url::fromArray($modelData);
    }

    protected function update(mixed $model): void
    {
        $sql = "UPDATE urls SET name = :make, created_at = :created_at WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $id = $model->getId();
        $name = $model->getName();
        $createdAt = $model->getCreatedAt();
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    protected function create(mixed $model): void
    {
        $sql = "INSERT INTO urls (name) VALUES (:name)";
        $stmt = $this->conn->prepare($sql);
        $name = $model->getName();
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $model->setId($id);
    }

    public function findByName(string $name): ?Url
    {
        $sql = "SELECT * FROM urls WHERE name = ?";
        $stmt = $this->conn->prepare($sql);
        $normalizedName = Url::normalizeName($name);
        $stmt->execute([$normalizedName]);
        if ($row = $stmt->fetch()) {
            $url = Url::fromArray($row);
            $url->setId($row['id']);
            return $url;
        }

        return null;
    }
}
