<?php
declare(strict_types = 1);

namespace App\Tests\Controller;

use App\Controller\ApiController;
use App\Service\FileCache;
use App\Service\Renderer;
use App\Service\SvgFileFactory;
use App\Tests\Model\Svg\SvgFileTest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;

class ApiControllerTest extends TestCase
{

    /**
     * @covers \App\Controller\ApiController::getFile()
     */
    public function testGetFile(): void
    {
        $controller = $this->makeController();
        $response = $controller->getFile('file: foo.svg', 'de', new Request());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('image/png', $response->headers->get('Content-Type'));
        self::assertStringEqualsFile(SvgFileTest::TEST_FILE_RENDERED, $response->getContent());
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

        $factory = new SvgFileFactory(new NullLogger());

        /** @var FileCache $cache */
        $controller = new ApiController($cache, new Renderer('rsvg-convert'), $factory);
        $controller->setContainer($container);

        return $controller;
    }
}
