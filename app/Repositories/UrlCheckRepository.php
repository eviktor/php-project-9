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
        throw new \Exception('Not implemented');
    }
}
