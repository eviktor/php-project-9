<?php

namespace App\Controllers;

use App\Database\Connection;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class UrlsController
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $db = $this->container->get(Connection::class)->connect();
        $sql = 'SELECT id, name, created_at FROM urls';
        $urls = $db->query($sql)->fetchAll();

        return $this->container->get(Twig::class)->render($response, 'urls/index.html.twig', compact('urls'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();

        $db = $this->container->get(Connection::class)->connect();
        $sql = 'INSERT INTO urls (name) VALUES (:name)';
        $stmt = $db->prepare($sql);
        $stmt->execute(['name' => $params['url']['name']]);
        // $id = $db->lastInsertId();

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $redirectUrl = $routeParser->urlFor('urls.index');
        return $response
             ->withHeader('Location', $redirectUrl)
             ->withStatus(302);
    }
}
