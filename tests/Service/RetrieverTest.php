<?php
declare(strict_types = 1);

namespace App\Tests\Service;

use App\Service\MediaWikiApi;
use App\Service\Retriever;
use PHPUnit\Framework\TestCase;

class RetrieverTest extends TestCase
{
    /**
     * @covers \App\Service\Retriever::retrieve()
     */
    public function testRetrieve(): void
    {
        $apiJson = file_get_contents(__DIR__.'/../data/imageinfo.json');
        $apiResult = \GuzzleHttp\json_decode($apiJson, true);

        $api = $this->getMockBuilder(MediaWikiApi::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([])
            ->getMock();

        $api->method('imageInfo')
            ->with('File:Test.svg')
            ->willReturn($apiResult);

        /** @var MediaWikiApi $api */
        $retriever = $this->getMockBuilder(Retriever::class)
            ->setConstructorArgs([$api])
            ->setMethods(['httpGet'])
            ->getMock();

        $retriever->expects(self::once())
            ->method('httpGet')
            ->with('https://upload.wikimedia.org/wikipedia/commons/b/bd/Test.svg')
            ->willReturn('test content');

        /** @var Retriever $retriever */
        self::assertEquals('test content', $retriever->retrieve('File:Test.svg'));
    }
}
