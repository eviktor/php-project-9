<?php

namespace App\Services;

use App\Helpers\UrlNameNormalizer;
use App\Helpers\UrlRequestChecker;
use App\Models\Url;
use App\Models\UrlCheck;
use App\Repositories\UrlCheckRepository;
use App\Repositories\UrlRepository;
use App\Validators\UrlValidator;
use GuzzleHttp\Psr7\Exception\MalformedUriException;
use Psr\Log\LoggerInterface;

class UrlService
{
    public function __construct(
        private UrlRepository $urlRepository,
        private UrlRequestChecker $checker,
        private UrlCheckRepository $checkRepository,
        private UrlValidator $urlValidator,
        private LoggerInterface $logger
    ) {
    }

    public function findUrlRecord(string $url): ?Url
    {
        return $this->urlRepository->findByName($url);
    }

    public function getUrlRecord(int $id): ?Url
    {
        return $this->urlRepository->find($id);
    }

    public function getAllUrlRecords(): array
    {
        return $this->urlRepository->getEntities('created_at DESC');
    }

    public function getUrlChecks(?int $urlId): array
    {
        if ($urlId === null) {
            return [];
        }
        return $this->checkRepository->findByUrlId($urlId);
    }

    public function saveUrl(string $url): Url
    {
        $this->logger->info('UrlService.saveUrl: ' . $url);
        $url = Url::fromArray(['name' => $url]);
        $this->urlRepository->save($url);
        return $url;
    }

    public function validateUrl(string $url): array
    {
        $normUrl = $this->normalizeUrl($url);
        if ($normUrl === false) {
            return ['url' => 'Некорректный URL'];
        }
        return $this->urlValidator->validate($normUrl);
    }

    public function normalizeUrl(string $url): string | false
    {
        try {
            return UrlNameNormalizer::normalize($url);
        } catch (MalformedUriException $e) {
            $this->logger->info('UrlService.normalizeUrl: ' . $e->getMessage());
            return false;
        }
    }

    public function checkUrl(Url $urlRec): ?UrlCheck
    {
        $result = $this->checker->checkUrl($urlRec->getName());
        if ($result === false) {
            return null;
        }
        $result['url_id'] = $urlRec->getId();
        $check = UrlCheck::fromArray($result);
        $this->checkRepository->save($check);
        return $check;
    }
}
