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
            INSERT INTO urls('name') VALUES
                ('http://example.com'),
                ('https://google.com')
            ;
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
}
