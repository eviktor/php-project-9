<?php

namespace App\Tests;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class Test extends TestCase
{
    /**
     * @var App<ContainerInterface>
     */
    protected static App $app;

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
        $req = $this->createRequest('GET', $path);
        return self::$app->handle($req);
    }

    protected function getResponseHtml(ResponseInterface $response): string
    {
        $body = $response->getBody();
        $body->rewind();
        return $body->getContents();
    }

    protected function post(string $path, array $params): ResponseInterface
    {
        $req = $this
            ->createRequest('POST', $path)
            ->withParsedBody($params);
        return self::$app->handle($req);
    }

    public static function setUpBeforeClass(): void
    {
        self::$app = require __DIR__ . '/../bootstrap/app.php';
        $pdo = self::$app->getContainer()->get(\PDO::class);

        $initFilePath = __DIR__ . "/../database.sqlite.sql";
        $initSql = file_get_contents($initFilePath);
        $pdo->exec($initSql);

        $initSql = <<<SQL
            INSERT INTO urls(name) VALUES ('http://example.com');
            INSERT INTO urls(name) VALUES ('https://google.com');
            INSERT INTO urls(name) VALUES ('http://some-not-existsing-domain.com');

            INSERT INTO url_checks(url_id, status_code, h1, title, description) VALUES
                (1, 200, 'Example h1', 'Example title', 'Example description');
        SQL;
        $pdo->exec($initSql);
    }

    protected function setUp(): void
    {
        $pdo = self::$app->getContainer()->get(\PDO::class);
        $pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $pdo = self::$app->getContainer()->get(\PDO::class);
        $pdo->rollBack();
    }

    public function testHome(): void
    {
        $response = $this->get('/');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testUrlCreate(): void
    {
        $params = ['url' => ['name' => 'http://test.com']];
        $response = $this->post('/urls', $params);
        $this->assertSame(302, $response->getStatusCode());
        $redirectUrl = $response->getHeader('Location')[0];
        $this->assertStringMatchesFormat('/urls/%d', $redirectUrl);

        $response = $this->get('/urls');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('Страница успешно добавлена', $html);
        $this->assertStringContainsString('http://test.com', $html);

        $response = $this->post('/urls', $params);
        $this->assertSame(302, $response->getStatusCode());
        $newRedirectUrl = $response->getHeader('Location')[0];
        $this->assertStringMatchesFormat('/urls/%d', $newRedirectUrl);
        $this->assertSame($redirectUrl, $newRedirectUrl);

        $response = $this->get('/urls');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('Страница уже существует', $html);
        $this->assertStringContainsString('http://test.com', $html);
    }

    public static function provideValidationData(): array
    {
        return [
            [ 'http://test.com', 302,'' ],
            [ 'test.com', 302,'' ],
            [ '-', 422, 'Некорректный URL' ],
            [ '', 422, 'URL не должен быть пустым' ],
        ];
    }

    #[DataProvider('provideValidationData')]
    public function testUrlValidation(string $url, int $expectedCode, string $expectedText): void
    {
        $params = ['url' => ['name' => $url]];
        $response = $this->post('/urls', $params);
        $this->assertSame($expectedCode, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString($expectedText, $html);
    }

    public function testUrlView(): void
    {
        $response = $this->get('/urls/1');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('example.com', $html);

        $response = $this->get('/urls/100');
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testUrlIndex(): void
    {
        $response = $this->get('/urls');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('example.com', $html);
        $this->assertStringContainsString('google.com', $html);
    }

    public function testCheckCreate(): void
    {
        $params = ['check' => ['url_id' => 1]];
        $response = $this->post('/urls/1/checks', $params);
        $this->assertSame(302, $response->getStatusCode());

        $response = $this->get('/urls/1');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('http://example.com', $html);
        $this->assertStringContainsString('Страница успешно проверена', $html);

        $params = ['check' => ['url_id' => 3]];
        $response = $this->post('/urls/3/checks', $params);
        $this->assertSame(302, $response->getStatusCode());

        $response = $this->get('/urls/3');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('http://some-not-existsing-domain.com', $html);
        $this->assertStringContainsString('Произошла ошибка при проверке, не удалось подключиться', $html);
    }

    public function testCheckIndex(): void
    {
        $response = $this->get('/urls/1');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('example.com', $html);
        $this->assertStringContainsString('Example h1', $html);
        $this->assertStringContainsString('Example title', $html);
        $this->assertStringContainsString('Example description', $html);
    }
}
