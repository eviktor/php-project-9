<?php

namespace App\Helpers;

use DOMDocument;
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
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $h1 = $doc->getElementsByTagName('h1')->item(0)?->textContent;
            $title = $doc->getElementsByTagName('title')->item(0)?->textContent;
            $metaItems = $doc->getElementsByTagName('meta');
            $description = '';
            foreach ($metaItems as $metaItem) {
                if ($metaItem->getAttribute('name') === 'description') {
                    $description = $metaItem->getAttribute('content');
                    break;
                }
            }

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
