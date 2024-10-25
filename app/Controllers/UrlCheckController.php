<?php

namespace App\Controllers;

use App\Models\UrlCheck;
use App\Repositories\UrlCheckRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UrlCheckController extends Controller
{
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("UrlChecks.create visited");

        $urlId = $args['url_id'];

        $checkRepository = $this->container->get(UrlCheckRepository::class);
        $check = UrlCheck::fromArray(['url_id' => $urlId]);
        $checkRepository->save($check);
        $this->flash->addMessage('success', 'Проверка успешно добавлена');

        $redirectUrl = $this->getRouteParser($request)->urlFor('urls.show', ['id' => $urlId]);
        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }
}
