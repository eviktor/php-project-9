<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController extends Controller
{
    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info("Home page visited");
        return $this->container->get(Twig::class)->render($response, 'home.html.twig');
    }
}
