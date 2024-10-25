<?php

namespace App\Repositories;

use App\Models\Model;
use App\Models\Url;
use App\Models\UrlCheck;

class UrlCheckRepository extends Repository
{
    protected string $tableName = 'url_checks';

    protected function makeModelFromArray(array $modelData): Model
    {
        return UrlCheck::fromArray($modelData);
    }

    protected function update(mixed $model): void
    {
        throw new \Exception('Not implemented');
    }

    protected function create(mixed $model): void
    {
        $sql = <<<SQL
            INSERT INTO url_checks(url_id, status_code, h1, title, description)
            VALUES (:url_id, :status_code, :h1, :title, :description)
        SQL;
        $stmt = $this->conn->prepare($sql);
        $urlId = $model->getUrlId();
        $stmt->bindParam(':url_id', $urlId);
        $statusCode = $model->getStatusCode();
        $stmt->bindParam(':status_code', $statusCode);
        $h1 = $model->getH1();
        $stmt->bindParam(':h1', $h1);
        $title = $model->getTitle();
        $stmt->bindParam(':title', $title);
        $description = $model->getDescription();
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $model->setId($id);
    }

    public function findByUrlId(int $urlId): array
    {
        $sql = "SELECT * FROM url_checks WHERE url_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$urlId]);

        $records = [];
        while ($row = $stmt->fetch()) {
            $record = $this->makeModelFromArray($row);
            $record->setId($row['id']);
            $records[] = $record;
        }
        return $records;
    }
}
