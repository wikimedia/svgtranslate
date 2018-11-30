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
        $crawler = $client->request('GET', '/File:Test.svg');
        $response = $client->getResponse();
        static::assertEquals(200, $response->getStatusCode());
        static::assertContains('Test.svg', $crawler->filter('h1')->text());
    }
}
