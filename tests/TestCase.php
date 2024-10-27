<?php

declare(strict_types=1);

namespace App\Tests;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

// Based on https://github.com/slimphp/Slim-Skeleton/blob/main/tests/TestCase.php
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var App<ContainerInterface>
     */
    protected static ?App $app = null;

    /**
     * @return App<ContainerInterface>
     * @throws Exception
     */
    protected static function getAppInstance(): App
    {
        if (is_null(self::$app)) {
            self::$app = require __DIR__ . '/../bootstrap/app.php';
        }
        return self::$app;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $headers
     * @param array  $cookies
     * @param array  $serverParams
     * @return Request
     */
    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        if ($handle === false) {
            throw new Exception('Unable to open temporary file');
        }
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    protected function get(string $path): ResponseInterface
    {
        $app = self::getAppInstance();
        $req = $this->createRequest('GET', $path);
        return $app->handle($req);
    }

    protected function getResponseHtml(ResponseInterface $response): string
    {
        $body = $response->getBody();
        $body->rewind();
        return $body->getContents();
    }

    protected function post(string $path, array $params): ResponseInterface
    {
        $app = self::getAppInstance();
        $req = $this
            ->createRequest('POST', $path)
            ->withParsedBody($params);
        return $app->handle($req);
    }
}
