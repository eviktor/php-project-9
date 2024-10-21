<?php

namespace App\Controllers;

use App\Models\Url;
use App\Repositories\UrlRepository;
use App\Validators\UrlValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class UrlsController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("Urls.index visited");

        $urlRepository = $this->container->get(UrlRepository::class);

        $params = [
            'urls' => $urlRepository->getEntities(),
            'flash' => $this->flash->getMessages()
        ];

        return $this->container->get(Twig::class)->render($response, 'urls/index.html.twig', $params);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("Urls.create visited");

        $params = (array)$request->getParsedBody();

        $urlRepository = $this->container->get(UrlRepository::class);
        $validator = $this->container->get(UrlValidator::class);
        $errors = $validator->validate($params);

        if (count($errors) === 0) {
            $url = Url::fromArray($params['url']);
            $urlRepository->save($url);

            $this->flash->addMessage('success', 'Страница успешно добавлена');
            $redirectUrl = $this->getRouteParser($request)->urlFor('urls.index');
            return $response
                ->withHeader('Location', $redirectUrl)
                ->withStatus(302);
        }

        $this->flash->addMessage('warning', 'Страница уже существует');
        $redirectUrl = $this->getRouteParser($request)->urlFor('urls.index');
        return $response
            ->withHeader('Location', $redirectUrl)
            ->withStatus(302);
    }
}
