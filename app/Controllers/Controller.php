<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

abstract class Controller
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function getRouteParser(ServerRequestInterface $request): RouteParserInterface
    {
        return RouteContext::fromRequest($request)->getRouteParser();
    }
}
