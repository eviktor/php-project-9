<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UrlCheckController extends Controller
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("UrlChecks.create visited");

        $urlId = $args['url_id'];
        $this->urlService->saveCheck($urlId);

        $this->flash->addMessage('success', 'Проверка успешно добавлена');

        $redirectUrl = $this->getRouteParser($request)->urlFor('urls.show', ['id' => $urlId]);
        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }
}
