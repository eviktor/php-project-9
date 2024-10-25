<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface as SlimResponseInterface;
use Slim\Views\Twig;

class UrlController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("Urls.index visited");

        $params = [
            'urls' => $this->urlService->getAllUrlRecords(),
            'flash' => $this->flash->getMessages()
        ];

        return $this->container->get(Twig::class)->render($response, 'urls/index.html.twig', $params);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $url = $params['url']['name'];

        $this->logger->info("Urls.create visited (url = $url)");

        $errors = $this->urlService->validateUrl($url);
        if (count($errors) > 0) {
            $homeParams = [
                'url' => $params['url'],
                'errors' => $errors['url']
            ];
            return $this->container->get(Twig::class)
                ->render($response->withStatus(422), 'home.html.twig', $homeParams);
        }

        $urlRec = $this->urlService->findUrlRecord($url);
        if ($urlRec !== null) {
            $this->flash->addMessage('warning', 'Страница уже существует');
        } else {
            $urlRec = $this->urlService->saveUrl($url);
            $this->flash->addMessage('success', 'Страница успешно добавлена');
        }

        $redirectUrl = $this->getRouteParser($request)
            ->urlFor('urls.show', ['id' => (string)$urlRec->getId()]);
        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }

    public function show(
        ServerRequestInterface $request,
        SlimResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->logger->info("Urls.show visited");

        $urlRec = $this->urlService->getUrlRecord((int)$args['id']);
        if ($urlRec === null) {
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('Page not found');
        }

        $params = [
            'url' => $urlRec,
            'checks' => $this->urlService->getUrlChecks($urlRec->getId()),
            'flash' => $this->flash->getMessages()
        ];
        return $this->container->get(Twig::class)->render($response, 'urls/show.html.twig', $params);
    }
}
