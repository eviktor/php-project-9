<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class UrlsController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->view->render($response, 'urls/index.html.twig');
    }
}
