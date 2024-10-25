<?php

namespace App\Models;

use Carbon\Carbon;

class UrlCheck extends Model
{
    private ?int $url_id = null;
    private ?int $status_code = null;
    private ?string $h1 = null;
    private ?string $title = null;
    private ?string $desciption = null;

    public static function fromArray(array $checkData): UrlCheck
    {
        $check = new UrlCheck();
        $check->setUrlId($checkData['url_id']);
        $check->setStatusCode($checkData['status_code']);
        $check->setH1($checkData['h1']);
        $check->setTitle($checkData['title']);
        $check->setDescription($checkData['description']);
        if (array_key_exists('created_at', $checkData)) {
            $check->setCreatedAt(Carbon::parse($checkData['created_at']));
        }

        return $check;
    }

    public function getUrlId(): ?int
    {
        return $this->url_id;
    }

    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function getH1(): ?string
    {
        return $this->h1;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->desciption;
    }

    public function setUrlId(int $urlId): void
    {
        $this->url_id = $urlId;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->status_code = $statusCode;
    }

    public function setH1(string $h1): void
    {
        $this->h1 = $h1;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $desciption): void
    {
        $this->desciption = $desciption;
    }
}
