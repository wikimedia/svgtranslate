<?php
declare(strict_types = 1);

namespace App\Tests\Controller;

use App\Controller\ApiController;
use App\Service\FileCache;
use App\Tests\Model\Svg\SvgFileTest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiControllerTest extends TestCase
{
    /**
     * @covers \App\Controller\ApiController::getFile()
     * @covers \App\Controller\ApiController::serveContent()
     */
    public function testGetFile(): void
    {
        $controller = $this->makeController();

        $response = $controller->getFile('file: foo.svg');
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('image/svg+xml', $response->headers->get('Content-Type'));
        self::assertEquals('test content', $response->getContent());
    }

    /**
     * @group Broken
     * https://phabricator.wikimedia.org/T209906
     */
    public function testGetFileWithTranslations(): void
    {
        $controller = $this->makeController();

        $translations = SvgFileTest::EXPECTED_TRANSLATIONS;
        $translations['tspan2987']['ru'] = [
            'text' => 'Привет',
            'x' => '80',
            'y' => '108.07646',
            'id' => 'tspan2987-ru',
        ];
        $request = new Request([], [], [], [], [], [], \GuzzleHttp\json_encode($translations));

        $response = $controller->getFileWithTranslations('Foo.svg', $request);
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('image/svg+xml', $response->headers->get('Content-Type'));
        self::assertNotFalse(strpos($response->getContent(), 'Прив1ет'));
    }

    /**
     * @covers \App\Controller\ApiController::getTranslations()
     */
    public function testGetTranslations(): void
    {
        $controller = $this->makeController();

        $response = $controller->getTranslations('Foo.svg');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
        $json = \GuzzleHttp\json_decode($response->getContent(), true);
        self::assertEquals(SvgFileTest::EXPECTED_TRANSLATIONS, $json);
    }

    /**
     * @covers \App\Controller\ApiController::getLanguages()
     */
    public function testGetLanguages(): void
    {
        $controller = $this->makeController();

        $response = $controller->getLanguages('Foo.svg');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
        $json = \GuzzleHttp\json_decode($response->getContent(), true);
        self::assertEquals(['de', 'fallback', 'fr', 'nl', 'tlh-ca'], $json);
    }

    private function makeController(): ApiController
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $cache = $this->getMockBuilder(FileCache::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept()
            ->getMock();

        $cache->method('getPath')
            ->with('Foo.svg')
            ->willReturn(SvgFileTest::TEST_FILE);

        $cache->method('getContent')
            ->with('Foo.svg')
            ->willReturn('test content');

        /** @var FileCache $cache */
        $controller = new ApiController($cache);
        $controller->setContainer($container);

        return $controller;
    }
}
