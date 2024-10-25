<?php

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

class Test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $app = self::getAppInstance();
        $pdo = $app->getContainer()?->get(\PDO::class);
        $initSql = <<<SQL
            INSERT INTO urls(name) VALUES
                ('http://example.com'),
                ('https://google.com'),
                ('http://some-not-existsing-domain.com')
            ;
            INSERT INTO url_checks(url_id, status_code, h1, title, description) VALUES
                (1, 200, 'Example h1', 'Example title', 'Example description');
        SQL;
        $pdo->exec($initSql);
    }

    protected function setUp(): void
    {
        $app = self::getAppInstance();
        $pdo = $app->getContainer()?->get(\PDO::class);
        $pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $app = self::getAppInstance();
        $pdo = $app->getContainer()?->get(\PDO::class);
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
        // $this->assertStringContainsString('Страница успешно проверена', $html);

        $params = ['check' => ['url_id' => 3]];
        $response = $this->post('/urls/3/checks', $params);
        $this->assertSame(302, $response->getStatusCode());

        $response = $this->get('/urls/3');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('http://some-not-existsing-domain.com', $html);
        // $this->assertStringContainsString('Произошла ошибка при проверке, не удалось подключиться', $html);
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
