<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface as SlimResponseInterface;

class UrlCheckController extends Controller
{
    public function create(
        ServerRequestInterface $request,
        SlimResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->logger->info("UrlChecks.create visited");

        $urlId = $args['url_id'];
        $urlRec = $this->urlService->getUrlRecord((int)$urlId);
        if ($urlRec === null) {
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found');
        }
        $checkRec = $this->urlService->checkUrl($urlRec);

        if ($checkRec === null) {
            $this->flash->addMessage('danger', 'Произошла ошибка при проверке, не удалось подключиться');
        } else {
            $this->flash->addMessage('success', 'Страница успешно проверена');
        }

        $redirectUrl = $this->getRouteParser($request)->urlFor('urls.show', ['id' => $urlId]);
        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }
}
