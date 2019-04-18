<?php
/**
 * Unit tests
 *
 * @file
 */

declare(strict_types = 1);

namespace App\Tests\Model\Svg;

use App\Model\Svg\SvgFile;
use DOMDocument;
use PHPUnit\Framework\TestCase;

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

    public function testGetSavedLanguagesFiltered(): void
    {
        $expected = [
            'full' => [ 'tlh-ca', 'fr', 'nl', 'fallback' ],
            'partial' => [ 'de' ],
        ];
        $this->assertEquals($expected, $this->getSvg()->getSavedLanguagesFiltered());
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
        static::assertContains('FooBarX', $svg->saveToString());

        // Test that multiple languages can be set, and modified.
        $svg->setTranslations('es', ['tspan2993' => 'FooBarModified']);
        $svg->setTranslations('fr', ['tspan2993' => 'BarFoo']);

        $svgContent = $svg->saveToString();
        static::assertContains('FooBarModified', $svgContent);
        static::assertContains('BarFoo', $svgContent);
        static::assertNotContains('FooBarX', $svgContent);
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
}
