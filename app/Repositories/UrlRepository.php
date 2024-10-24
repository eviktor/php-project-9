<?php

namespace App\Repositories;

use App\Models\Url;

class UrlRepository extends Repository
{
    public function getEntities(string $order = ''): array
    {
        $urls = [];
        $sql = "SELECT * FROM urls" . ($order !== '' ? " ORDER BY $order" : '');
        $stmt = $this->conn->query($sql);

        if ($stmt === false) {
            throw new \Exception('Query failed: ' . implode(', ', $this->conn->errorInfo()));
        }

        while ($row = $stmt->fetch()) {
            $url = Url::fromArray($row);
            $url->setId($row['id']);
            $urls[] = $url;
        }

        return $urls;
    }

    public function find(int $id): ?Url
    {
        $sql = "SELECT * FROM urls WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $url = Url::fromArray($row);
            $url->setId($row['id']);
            return $url;
        }

        return null;
    }

    public function save(Url $url): void
    {
        if ($url->exists()) {
            $this->update($url);
        } else {
            $this->create($url);
        }
    }

    private function update(Url $url): void
    {
        $sql = "UPDATE urls SET name = :make, created_at = :created_at WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $id = $url->getId();
        $name = $url->getName();
        $createdAt = $url->getCreatedAt();
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    private function create(Url $url): void
    {
        $sql = "INSERT INTO urls (name) VALUES (:name)";
        $stmt = $this->conn->prepare($sql);
        $name = $url->getName();
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $url->setId($id);
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
