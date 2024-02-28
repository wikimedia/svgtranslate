<?php

declare(strict_types = 1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TranslateControllerTest extends WebTestCase
{

    /**
     * The translate page.
     */
    public function testExists(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/File:SVG_Translate_test_-_valid.svg');
        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('SVG Translate test - valid.svg', $crawler->filter('h1')->text());
    }

    public function testNonSvgHandling(): void
    {
        $client = static::createClient();
        $client->request('GET', '/File:Test.jpg');
        $response = $client->getResponse();
        static::assertEquals(302, $response->getStatusCode());
    }
}
