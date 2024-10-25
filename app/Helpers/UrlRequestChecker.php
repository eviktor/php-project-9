<?php

namespace App\Helpers;

use DiDom\Document;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class UrlRequestChecker
{
    public function __construct(private LoggerInterface $loggger)
    {
    }

    public function checkUrl(string $url): array|false
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $url);
            $statusCode = $response->getStatusCode();
            $html = $response->getBody()->getContents();
            $doc = new Document($html);
            // @phpstan-ignore-next-line
            $h1 = $doc->first('h1')?->text();
            // @phpstan-ignore-next-line
            $title = $doc->first('title')?->text();
            // @phpstan-ignore-next-line
            $description = $doc->first('meta[name=description]')?->attr('content');

            return [
                'status_code' => $statusCode,
                'h1' => $h1,
                'title' => $title,
                'description' => $description,
            ];
        } catch (\Throwable $e) {
            $this->loggger->info("UrlRequestChecker.checkUrl ($url): " . $e->getMessage());
            return false;
        }
    }
}
