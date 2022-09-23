<?php
/**
 * Unit tests
 *
 * @file
 */

declare(strict_types = 1);

namespace App\Tests\Model\Svg;

use App\Exception\SvgLoadException;
use App\Exception\SvgStructureException;
use App\Model\Svg\SvgFile;
use DOMDocument;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Logger;

/**
 * Unit tests for SVGFile class.
 * @covers \App\Model\Svg\SvgFile
 */
class SvgFileTest extends TestCase
{
    public const TEST_FILE = __DIR__.'/../../data/Speech_bubbles.svg';

    /**
     * This PNG file is generated as part of the Composer installation process,
     * because rsvg is not deterministic.
     */
    public const TEST_FILE_RENDERED = __DIR__.'/../../data/Speech_bubbles.png';

    public const EXPECTED_TRANSLATIONS = [
        'tspan2987' =>
            [
                'de' =>
                    [
                        'text' => 'Hallo!',
                        'id' => 'tspan2987-de',
                        'data-parent' => 'text2985',
                    ],
                'fr' =>
                    [
                        'text' => 'Bonjour',
                        'x' => '80',
                        'y' => '108.07646',
                        'id' => 'tspan2987-fr',
                        'data-parent' => 'text2985',
                    ],
                'nl' =>
                    [
                        'text' => 'Hallo!',
                        'x' => '90',
                        'y' => '108.07646',
                        'id' => 'tspan2987-nl',
                        'data-parent' => 'text2985',
                    ],
                'tlh-ca' =>
                    [
                        'text' => 'Hallo!',
                        'x' => '90',
                        'y' => '108.07646',
                        'id' => 'tspan2987-nl',
                        'data-parent' => 'text2985',
                    ],
                'fallback' =>
                    [
                        'text' => 'Hello!',
                        'x' => '90',
                        'y' => '108.07646',
                        'id' => 'tspan2987',
                        'sodipodi:role' => 'line',
                        'data-parent' => 'text2985',
                    ],
            ],
        'tspan2991' =>
            [
                'de' =>
                    [
                        'text' => 'Hallo! Wie',
                        'x' => '323',
                        'y' => '188.07648',
                        'id' => 'tspan2991-de',
                        'data-parent' => 'text2989',
                    ],
                'fr' =>
                    [
                        'text' => 'Bonjour,',
                        'x' => '335',
                        'y' => '188.07648',
                        'id' => 'tspan2991-fr',
                        'data-parent' => 'text2989',
                    ],
                'nl' =>
                    [
                        'text' => 'Hallo! Hoe',
                        'x' => '310',
                        'y' => '188.07648',
                        'id' => 'tspan2991-nl',
                        'data-parent' => 'text2989',
                    ],
                'tlh-ca' =>
                    [
                        'text' => 'Hallo! Hoe',
                        'x' => '310',
                        'y' => '188.07648',
                        'id' => 'tspan2991-nl',
                        'data-parent' => 'text2989',
                    ],
                'fallback' =>
                    [
                        'text' => 'Hello! How',
                        'x' => '330',
                        'y' => '188.07648',
                        'id' => 'tspan2991',
                        'sodipodi:role' => 'line',
                        'data-parent' => 'text2989',
                        'text-decoration' => 'normal',
                        'font-style' => 'normal',
                        'font-weight' => 'normal',
                    ],
            ],
        'tspan2993' =>
            [
                'de' =>
                    [
                        'text' => 'geht\'s?',
                        'x' => '350',
                        'y' => '238.07648',
                        'id' => 'tspan2993-de',
                        'sodipodi:role' => 'line',
                        'data-parent' => 'text2989',
                    ],
                'fr' =>
                    [
                        'text' => 'ça va?',
                        'x' => '350',
                        'y' => '238.07648',
                        'id' => 'tspan2993-fr',
                        'data-parent' => 'text2989',
                    ],
                'nl' =>
                    [
                        'text' => 'gaat het?',
                        'x' => '330',
                        'y' => '238.07648',
                        'id' => 'tspan2993-nl',
                        'data-parent' => 'text2989',
                    ],
                'tlh-ca' =>
                    [
                        'text' => 'gaat het?',
                        'x' => '330',
                        'y' => '238.07648',
                        'id' => 'tspan2993-nl',
                        'data-parent' => 'text2989',
                    ],
                'fallback' =>
                    [
                        'text' => 'are you?',
                        'x' => '330',
                        'y' => '238.07648',
                        'id' => 'tspan2993',
                        'sodipodi:role' => 'line',
                        'data-parent' => 'text2989',
                    ],
            ],
        'tspan2997' =>
            [
                'fr' =>
                    [
                        'text' => 'Ça va bien,',
                        'x' => '82',
                        'y' => '323',
                        'id' => 'tspan2997-fr',
                        'data-parent' => 'text2995',
                        'font-weight' => 'normal',
                    ],
                'nl' =>
                    [
                        'text' => 'Goed,',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'tspan2997-nl',
                        'data-parent' => 'text2995',
                        'font-style' => 'normal',
                    ],
                'tlh-ca' =>
                    [
                        'text' => 'Goed,',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'tspan2997-nl',
                        'data-parent' => 'text2995',
                        'font-style' => 'normal',
                    ],
                'fallback' =>
                    [
                        'text' => 'I\'m well,',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'tspan2997',
                        'sodipodi:role' => 'line',
                        'data-parent' => 'text2995',
                        'text-decoration' => 'normal',
                    ],
            ],
        'tspan2999' =>
            [
                'fr' =>
                    [
                        'text' => 'et toi?',
                        'x' => '117.42857',
                        'y' => '368.64789',
                        'id' => 'tspan2999-fr',
                        'data-parent' => 'text2995',
                    ],
                'nl' =>
                    [
                        'text' => 'met jou?',
                        'x' => '101.42857',
                        'y' => '368.64789',
                        'font-size' => '90%',
                        'id' => 'tspan2999-nl',
                        'data-parent' => 'text2995',
                    ],
                'tlh-ca' =>
                    [
                        'text' => 'met jou?',
                        'x' => '101.42857',
                        'y' => '368.64789',
                        'font-size' => '90%',
                        'id' => 'tspan2999-nl',
                        'data-parent' => 'text2995',
                    ],
                'fallback' =>
                    [
                        'text' => '   you?',
                        'x' => '101.42857',
                        'y' => '368.64789',
                        'id' => 'tspan2999',
                        'sodipodi:role' => 'line',
                        'data-parent' => 'text2995',
                    ],
            ],
    ];

    private function getSvg(string $fileName = 'Speech_bubbles.svg'): SvgFile
    {
        return new SvgFile(__DIR__."/../../data/$fileName");
    }

    /**
     * Create a new SvgFile object from an XML string.
     * @param string $svgContents Do not include the XML prologue.
     */
    protected function getSvgFileFromString( $svgContents )
    {
        $filename = dirname( __DIR__, 2 ) . '/data/_test.svg';
        file_put_contents( $filename, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . $svgContents );
        $logger = new Logger( 'svgtranslate' );
        $logger->pushHandler( new StreamHandler( 'php://stderr' ) );
        return new SvgFile( $filename, $logger );
    }

    /*
     * @todo: consider if data-parent needs to survive roundtrip, and, if so, how
     */
    public function testArrayToNodeToArray(): void
    {
        $array = [
            'text' => 'Hallo!',
            'id' => 'tspan2987-de',
            'font-weight' => 'bold',
            'text-anchor' => 'end',
            'data-parent' => 'text2985',
        ];

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<text id="tspan2987-de" font-weight="bold" text-anchor="end">Hallo!</text>');

        $svg = $this->getSvg();
        $node = $svg->arrayToNode($array, 'tspan');
        $this->assertEquals($svg->nodeToArray($dom->firstChild), $svg->nodeToArray($node));

        $expectedArray = $array;
        unset($expectedArray['data-parent']);
        $this->assertEquals($expectedArray, $svg->nodeToArray($node));
    }

    public function testGetInFileTranslations(): void
    {
        $this->assertEquals(self::EXPECTED_TRANSLATIONS, $this->getSvg()->getInFileTranslations());
    }

    public function testGetSavedLanguages(): void
    {
        $expected = [
            'tlh-ca', 'de', 'fr', 'nl', 'fallback',
        ];
        $this->assertEquals($expected, $this->getSvg()->getSavedLanguages());
    }

    public function testGetFilteredTextNodes(): void
    {
        // The important things here are:
        // * array length. One of the three sets has non-zero text content, so should not be filtered
        // * text. Since they are filtered, all should contain nothing but $ references.
        // * data-children. Each should have as many children as there are $ references.

        $expected = [
            'text2985' =>
                [
                    'de' =>
                        [
                            'text' => '$1',
                            'xml:space' => 'preserve',
                            'x' => '90',
                            'y' => '108.07646',
                            'id' => 'text2985-de',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2987',
                        ],
                    'fr' =>
                        [
                            'text' => '$1',
                            'xml:space' => 'preserve',
                            'x' => '90',
                            'y' => '108.07646',
                            'id' => 'text2985-fr',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2987',
                        ],
                    'nl' =>
                        [
                            'text' => '$1',
                            'xml:space' => 'preserve',
                            'x' => '90',
                            'y' => '108.07646',
                            'id' => 'text2985-nl',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2987',
                        ],
                    'tlh-ca' =>
                        [
                            'text' => '$1',
                            'xml:space' => 'preserve',
                            'x' => '90',
                            'y' => '108.07646',
                            'id' => 'text2985-nl',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2987',
                        ],
                    'fallback' =>
                        [
                            'text' => '$1',
                            'xml:space' => 'preserve',
                            'x' => '90',
                            'y' => '108.07646',
                            'id' => 'text2985',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2987',
                        ],
                ],
            'text2989' =>
                [
                    'de' =>
                        [
                            'text' => '$1$2',
                            'xml:space' => 'preserve',
                            'x' => '330',
                            'y' => '188.07648',
                            'id' => 'text2989-de',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2991|tspan2993',
                        ],
                    'fr' =>
                        [
                            'text' => '$1$2',
                            'xml:space' => 'preserve',
                            'x' => '330',
                            'y' => '188.07648',
                            'id' => 'text2989-fr',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2991|tspan2993',
                        ],
                    'nl' =>
                        [
                            'text' => '$1$2',
                            'xml:space' => 'preserve',
                            'x' => '330',
                            'y' => '188.07648',
                            'id' => 'text2989-nl',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2991|tspan2993',
                        ],
                    'tlh-ca' =>
                        [
                            'text' => '$1$2',
                            'xml:space' => 'preserve',
                            'x' => '330',
                            'y' => '188.07648',
                            'id' => 'text2989-nl',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2991|tspan2993',
                        ],
                    'fallback' =>
                        [
                            'text' => '$1$2',
                            'xml:space' => 'preserve',
                            'x' => '330',
                            'y' => '188.07648',
                            'id' => 'text2989',
                            'sodipodi:linespacing' => '125%',
                            'data-children' => 'tspan2991|tspan2993',
                        ],
                ],
            'text2995' => [
                'fr' =>
                    [
                        'text' => '$1$2',
                        'xml:space' => 'preserve',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'text2995-fr',
                        'sodipodi:linespacing' => '125%',
                        'data-children' => 'tspan2997|tspan2999',
                    ],
                'nl' =>
                    [
                        'text' => '$1$2',
                        'xml:space' => 'preserve',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'text2995-nl',
                        'sodipodi:linespacing' => '125%',
                        'data-children' => 'tspan2997|tspan2999',
                    ],
                'tlh-ca' =>
                    [
                        'text' => '$1$2',
                        'xml:space' => 'preserve',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'text2995-nl',
                        'sodipodi:linespacing' => '125%',
                        'data-children' => 'tspan2997|tspan2999',
                    ],
                'fallback' =>
                    [
                        'text' => '$1$2',
                        'xml:space' => 'preserve',
                        'x' => '101.42857',
                        'y' => '318.64789',
                        'id' => 'text2995',
                        'sodipodi:linespacing' => '125%',
                        'data-children' => 'tspan2997|tspan2999',
                    ],
                ],
        ];
        $this->assertEquals($expected, $this->getSvg()->getFilteredTextNodes());
    }

    public function testSwitchTranslationSetRoundtrip(): void
    {
        // Functions already tested above
        $svg = $this->getSvg();
        $origXml = $svg->saveToString();
        $current = $svg->getInFileTranslations();
        $filteredTextNodes = $svg->getFilteredTextNodes();
        $ret = $svg->switchToTranslationSet(array_merge($current, $filteredTextNodes));

        $this->assertEquals($current, $svg->getInFileTranslations());
        $this->assertEquals($filteredTextNodes, $svg->getFilteredTextNodes());
        $this->assertEquals([ 'started' => [], 'expanded' => [] ], $ret);

        $this->assertEquals(str_replace(' ', '', $origXml), str_replace(' ', '', $svg->saveToString()));
    }

    public function testSetTranslations(): void
    {
        // The test file does not contain Spanish.
        $svg = $this->getSvg();
        $svg->setTranslations('es', ['tspan2993' => 'FooBarX']);
        static::assertStringContainsString('FooBarX', $svg->saveToString());

        // Test that multiple languages can be set, and modified.
        $svg->setTranslations('es', ['tspan2993' => 'FooBarModified']);
        $svg->setTranslations('fr', ['tspan2993' => 'BarFoo']);

        $svgContent = $svg->saveToString();
        static::assertStringContainsString('FooBarModified', $svgContent);
        static::assertStringContainsString('BarFoo', $svgContent);
        static::assertStringNotContainsString('FooBarX', $svgContent);
    }

    public function testRemovesUnderscoresFromLangTags() {
        // Test that underscores are replaced with hyphens when loading an SVG.
        $svgFile = $this->getSvgFileFromString('<svg><switch><text systemLanguage="zh_HANT">foo</text><text>bar</text></switch></svg>');
        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<svg xmlns="http://www.w3.org/2000/svg"><switch>'
            . '<text systemLanguage="zh-hant" id="trsvg3"><tspan id="trsvg1">foo</tspan></text>'
            . '<text id="trsvg4"><tspan id="trsvg2">bar</tspan></text>'
            . '</switch></svg>' . "\n",
            $svgFile->saveToString()
        );
        $this->assertSame( ['zh-hant', 'fallback'], $svgFile->getSavedLanguages() );

        // And also when writing new languages to the file.
        $svgFile->setTranslations('en_gb', ['trsvg2' => 'baz']);
        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<svg xmlns="http://www.w3.org/2000/svg"><switch>'
            . '<text id="trsvg4-en-gb" systemLanguage="en-gb"><tspan id="trsvg2-en-gb">baz</tspan></text>'
            . '<text systemLanguage="zh-hant" id="trsvg3"><tspan id="trsvg1">foo</tspan></text>'
            . '<text id="trsvg4"><tspan id="trsvg2">bar</tspan></text>'
            . '</switch></svg>' . "\n",
            $svgFile->saveToString()
        );
        $this->assertSame(['en-gb', 'zh-hant', 'fallback'], $svgFile->getSavedLanguages());
    }

    /**
     * @covers \App\Model\Svg\SvgFile::makeTranslationReady()
     * @dataProvider provideSvgNamespace()
     */
    public function testSvgNamespace(string $input, string $expected) {
        $svgFile = $this->getSvgFileFromString('<svg xmlns:svg="http://www.w3.org/2000/svg">' . $input . '</svg>');
        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg">'
            . $expected
            . '</svg>' . "\n",
            $svgFile->saveToString()
        );
    }

    /**
     * Test various element namespace support, without making any changes to translations.
     *
     * @return string[][]
     */
    public function provideSvgNamespace() {
        return [
            'no_namespace' => [
                'input' => '<switch>'
                    . '<text id="trsvg18-zh-hans" systemLanguage="zh-Hans"><tspan id="trsvg2-zh-hans">A</tspan></text>'
                    . '<text id="trsvg18"><tspan id="trsvg2">B</tspan></text>'
                    . '</switch>',
                'expected' => '<switch>'
                    . '<text id="trsvg18-zh-hans" systemLanguage="zh-hans"><tspan id="trsvg2-zh-hans">A</tspan></text>'
                    . '<text id="trsvg18"><tspan id="trsvg2">B</tspan></text>'
                    . '</switch>',
            ],
            'switch_namespace' => [
                'input' => '<svg:switch>'
                    . '<text id="trsvg18-zh-hans" systemLanguage="zh-Hans"><tspan id="trsvg2-zh-hans">A</tspan></text>'
                    . '<text id="trsvg18"><tspan id="trsvg2">B</tspan></text>'
                    . '</svg:switch>',
                'expected' => '<svg:switch>'
                    . '<text id="trsvg18-zh-hans" systemLanguage="zh-hans"><tspan id="trsvg2-zh-hans">A</tspan></text>'
                    . '<text id="trsvg18"><tspan id="trsvg2">B</tspan></text>'
                    . '</svg:switch>',
            ],
            'text_namespace' => [
                'input' => '<switch>'
                    . '<svg:text id="trsvg18-zh-hans" systemLanguage="zh-Hans"><tspan id="trsvg2-zh-hans">A</tspan></svg:text>'
                    . '<svg:text id="trsvg18"><tspan id="trsvg2">B</tspan></svg:text>'
                    . '</switch>',
                'expected' => '<switch>'
                    . '<svg:text id="trsvg18-zh-hans" systemLanguage="zh-hans"><tspan id="trsvg2-zh-hans">A</tspan></svg:text>'
                    . '<svg:text id="trsvg18"><tspan id="trsvg2">B</tspan></svg:text>'
                    . '</switch>',
            ],
            'tspan_namespace' => [
                'input' => '<switch>'
                    . '<text id="trsvg18-zh-hans" systemLanguage="zh-Hans"><svg:tspan id="trsvg2-zh-hans">A</svg:tspan></text>'
                    . '<text id="trsvg18"><svg:tspan id="trsvg2">B</svg:tspan></text>'
                    . '</switch>',
                'expected' => '<switch>'
                    . '<text id="trsvg18-zh-hans" systemLanguage="zh-hans"><svg:tspan id="trsvg2-zh-hans">A</svg:tspan></text>'
                    . '<text id="trsvg18"><svg:tspan id="trsvg2">B</svg:tspan></text>'
                    . '</switch>',
            ],
        ];
    }

    public function testT214717(): void
    {
        $fileName = tempnam(sys_get_temp_dir(), 'SvgFile');
        $svg = $this->getSvg();
        $svg->setTranslations('ru', ['tspan2993' => '']);
        $svg->saveToPath($fileName);

        $newSvg = new SvgFile($fileName);
        unlink($fileName);
        $translations = $newSvg->getInFileTranslations();
        static::assertFalse(isset($translations['tspan2993']['ru']));
    }

    public function testSaveToString(): void
    {
        // Check that we are not actually destroying the XML file
        $this->assertGreaterThan(1500, strlen($this->getSvg()->saveToString()));
    }

    public function testSaveToPath(): void
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test');
        $this->getSvg()->saveToPath($tempPath);

        // Check that we are not actually destroying the XML file
        $this->assertGreaterThan(1500, strlen(file_get_contents($tempPath)));
    }

    public function testEmptySvg(): void
    {
        $file = $this->getSvg('empty.svg');
        $this->assertEquals([], $file->getInFileTranslations());
    }

    public function testUnevenTspans(): void
    {
        $file = $this->getSvg('tspans.svg');
        $this->assertEquals(
            [
                'trsvg3' => [
                    'ru' => [
                        'text' => 'RU',
                        'id' => 'trsvg1',
                        'data-parent' => 'trsvg6',
                    ],
                    'de' => [
                        'text' => 'DE',
                        'id' => 'trsvg2',
                        'data-parent' => 'trsvg6',

                    ],
                    'fallback' => [
                        'text' => 'fallback',
                        'id' => 'trsvg3',
                        'data-parent' => 'trsvg6',
                    ],
                ],
            ],
            $file->getInFileTranslations()
        );
    }

    /**
     * https://phabricator.wikimedia.org/T216283
     * Handle some parts of a <text> being <tspan> and some not
     */
    public function testMixedTextContent(): void
    {
        $svg = $this->getSvg('mixed.svg');
        $this->assertCount(6, $svg->getInFileTranslations());
    }

    /**
     * https://phabricator.wikimedia.org/T220522
     */
    public function testChildOnly(): void
    {
        $svg = $this->getSvg('child-only.svg');
        $svg->setTranslations('ru', ['trsvg1' => 'foo', 'trsvg2' => '']);
        // Dummy assertion to avoid this test being marked as risky; the measure of success here is no crash.
        static::assertTrue(true);
    }

    public function testInvalidFileHandling(): void
    {
        self::expectException(SvgLoadException::class);
        self::expectExceptionMessage("Start tag expected, '<' not found in invalid.svg line 1");
        $this->getSvg('invalid.svg');
    }

    /**
     * @dataProvider provideSvgStructureException()
     */
    public function testSvgStructureException(string $svg, string $message, array $params)
    {
        try {
            $this->getSvgFileFromString($svg);
        } catch (SvgStructureException $exception) {
            $this->assertSame($message, $exception->getMessage());
            $this->assertSame($params, $exception->getMessageParams());
            return;
        }
        // If exception not caught, this is a fail.
        $this->fail();
    }

    public function provideSvgStructureException()
    {
        return [
            'Simple nested tspan' => [
                'svg' => '<svg><text><tspan>foo <tspan>bar</tspan></tspan></text></svg>',
                'message' => 'structure-error-nested-tspans-not-supported',
                'params' => [0 => ''],
            ],
            'Nested tspan with ID' => [
                'svg' => '<svg><text><tspan id="test">foo <tspan>bar</tspan></tspan></text></svg>',
                'message' => 'structure-error-nested-tspans-not-supported',
                'params' => [0 => 'test'],
            ],
            'Nested tspan with grandparent with ID' => [
                'svg' => '<svg><g id="gparent"><text><tspan>foo <tspan>bar</tspan></tspan></text></g></svg>',
                'message' => 'structure-error-nested-tspans-not-supported',
                'params' => [0 => 'gparent'],
            ],
            'CSS too complex' => [
                'svg' => '<svg><style>#foo { stroke:1px; } .bar { color:pink; }</style><text>Foo</text></svg>',
                'message' => 'structure-error-css-too-complex',
                'params' => [0 => ''],
            ],
            'tref' => [
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs><text id="tref-id">Lorem</text></defs>
                    <text id="text"><tref xlink:href="#tref-id" /></text></svg>',
                'message' => 'structure-error-contains-tref',
                'params' => [0 => 'text'],
            ],
            'id-chars' => [
                'svg' => '<svg><text id="x|">Foo</text></svg>',
                'message' => 'structure-error-invalid-node-id',
                'params' => ['x|'],
            ],
            'Text with dollar numbers' => [
                'svg' => '<svg><text id="blah">Foo $3 bar</text></svg>',
                'message' => 'structure-error-text-contains-dollar',
                'params' => ['blah', 'Foo $3 bar'],
            ],
        ];
    }

    /**
     * Existing switch elements can contain text elements that will be replaced,
     * but not if there are multiple with the same systemLangauge.
     */
    public function testAddsTextToSwitch() {
        // No matching text element, so it adds one.
        $svgFile = $this->getSvgFileFromString('<svg><switch><text>lang none</text></switch></svg>');
        $svgFile->setTranslations('la', ['trsvg1' => 'lang la']);
        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<svg xmlns="http://www.w3.org/2000/svg"><switch>'
            . '<text id="trsvg2-la" systemLanguage="la"><tspan id="trsvg1-la">lang la</tspan></text>'
            . '<text id="trsvg2"><tspan id="trsvg1">lang none</tspan></text>'
            . '</switch></svg>' . "\n",
            $svgFile->saveToString()
        );

        // One matching text element, so it's updated.
        $svgFile2 = $this->getSvgFileFromString('<svg><switch><text systemLanguage="la">lang la</text><text>lang none</text></switch></svg>');
        $svgFile2->setTranslations('la', ['trsvg2' => 'lang la (new)']);
        $this->assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<svg xmlns="http://www.w3.org/2000/svg"><switch>'
            . '<text id="trsvg3" systemLanguage="la"><tspan id="trsvg1">lang la (new)</tspan></text>'
            . '<text id="trsvg4"><tspan id="trsvg2">lang none</tspan></text>'
            . '</switch></svg>' . "\n",
            $svgFile2->saveToString()
        );

        // If there are more than one text element with the same language, give up.
        try {
            $svgFile3 = $this->getSvgFileFromString('<svg><switch id="testswitch">'
                . '<text systemLanguage="la">lang la (1)</text>'
                . '<text systemLanguage="la">lang la (2)</text>'
                . '<text>lang none</text></switch>'
                . '</svg>');
                $svgFile3->setTranslations('la', ['trsvg3' => 'lang la (new)']);
        } catch (SvgStructureException $exception) {
            $this->assertSame('multiple-text-same-lang', $exception->getMessage());
            $this->assertSame(['testswitch', 'la'], $exception->getMessageParams());
        }
    }
}
