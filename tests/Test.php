<?php

namespace App\Tests;

class Test extends TestCase
{
    public function testAnalyzer(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', '/');
        $response = $app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
    }
}
