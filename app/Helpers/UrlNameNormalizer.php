<?php

declare(strict_types=1);

namespace App\Helpers;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;

final class UrlNameNormalizer
{
    public static function normalize(string $url): string
    {
        $preparedUrl = trim(strtolower($url));
        if ($preparedUrl === '') {
            return '';
        }
        if (!str_starts_with($preparedUrl, 'http')) {
            $preparedUrl = 'http://' . $preparedUrl;
        }
        $uri = new Uri($preparedUrl);
        $uri = $uri->withPath('');
        $uri = $uri->withQuery('');
        $url = UriNormalizer::normalize($uri);
        return rtrim((string)$uri, '/');
    }
}
