<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class UrlsController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $db = $this->container->get(\PDO::class);
        $sql = 'SELECT id, name, created_at FROM urls';
        $urls = $db->query($sql)->fetchAll();

        return $this->container->get(Twig::class)->render($response, 'urls/index.html.twig', compact('urls'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();

        $db = $this->container->get(\PDO::class);
        $sql = 'INSERT INTO urls (name) VALUES (:name)';
        $stmt = $db->prepare($sql);
        $stmt->execute(['name' => $params['url']['name']]);
        // $id = $db->lastInsertId();

        $redirectUrl = $this->getRouteParser($request)->urlFor('urls.index');
        return $response
             ->withHeader('Location', $redirectUrl)
             ->withStatus(302);
    }
}
