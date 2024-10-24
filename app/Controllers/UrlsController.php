<?php

namespace App\Controllers;

use App\Models\Url;
use App\Repositories\UrlRepository;
use App\Validators\UrlValidator;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Interfaces\ResponseInterface as SlimResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class UrlsController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("Urls.index visited");

        $urlRepository = $this->container->get(UrlRepository::class);

        $params = [
            'urls' => $urlRepository->getEntities('created_at DESC'),
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
        if (count($errors) > 0) {
            $homeParams = [
                'url' => $params['url'],
                'errors' => $errors['url.name']
            ];
            return $this->container->get(Twig::class)
                ->render($response->withStatus(422), 'home.html.twig', $homeParams);
        }

        $id = $urlRepository->findByName($params['url']['name'])?->getId();
        if ($id !== null) {
            $this->flash->addMessage('warning', 'Страница уже существует');
        } else {
            $url = Url::fromArray($params['url']);
            $urlRepository->save($url);
            $id = $url->getId();
            $this->flash->addMessage('success', 'Страница успешно добавлена');
        }

        $redirectUrl = $this->getRouteParser($request)->urlFor('urls.show', ['id' => $id]);
        return $response
            ->withHeader('Location', $redirectUrl)
            ->withStatus(302);
    }

    public function show(
        ServerRequestInterface $request,
        SlimResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->logger->info("Urls.show visited");

        $urlRepository = $this->container->get(UrlRepository::class);
        $url = $urlRepository->find((int)$args['id']);
        if ($url === null) {
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found');
        }

        $params = [
            'url' => $url,
            'flash' => $this->flash->getMessages()
        ];
        return $this->container->get(Twig::class)->render($response, 'urls/show.html.twig', $params);
    }
}
