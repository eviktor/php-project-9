<?php

namespace App\Tests;

class Test extends TestCase
{
    protected function initDb(): \PDO
    {
        $app = $this->getAppInstance();
        $pdo = $app->getContainer()?->get(\PDO::class);
        $initFilePath = __DIR__ . "/init.sql";
        $initSql = file_get_contents($initFilePath);
        $pdo->exec($initSql);
        return $pdo;
    }

    protected function setUp(): void
    {
        $this->initDb();
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
        $this->assertSame('/urls', $response->getHeader('Location')[0]);

        $response = $this->get('/urls');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('Страница успешно добавлена', $html);
        $this->assertStringContainsString('http://test.com', $html);

        $response = $this->post('/urls', $params);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/urls', $response->getHeader('Location')[0]);

        $response = $this->get('/urls');
        $this->assertSame(200, $response->getStatusCode());
        $html = $this->getResponseHtml($response);
        $this->assertStringContainsString('Страница уже существует', $html);
        $this->assertStringContainsString('http://test.com', $html);
    }
}
