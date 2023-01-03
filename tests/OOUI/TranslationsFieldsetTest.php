<?php

declare(strict_types = 1);

namespace App\Tests\OOUI;

use App\Model\Svg\SvgFile;
use App\OOUI\TranslationsFieldset;
use OOUI\FieldsetLayout;
use PHPUnit\Framework\TestCase;

class TranslationsFieldsetTest extends TestCase
{

    /**
     * @dataProvider fieldsetGroupingProvider()
     * @param string[] $translations
     */
    public function testFieldsetGrouping(array $translations, int $fieldsetCount, string $sourceLang = 'fallback'): void
    {
        $fieldset = new TranslationsFieldset([
            'translations' => $translations,
            'source_lang_code' => $sourceLang,
            'target_lang_code' => 'eo',
        ]);
        static::assertCount($fieldsetCount, $fieldset->getItems());
    }

    /**
     * @return string[][][]
     */
    public function fieldsetGroupingProvider(): array
    {
        $singleField = [
            'span1' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text1']],
        ];
        $twoUnrelatedFields = [
            'span1' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text1']],
            'span2' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text2']],
        ];
        $threeFieldsInTwoGroups = [
            'span1' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text1']],
            'span2A' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text2']],
            'span2B' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text2']],
        ];
        return [
            'single field' => [$singleField, 1],
            'two unrelated fields' => [$twoUnrelatedFields, 2],
            'three fields in two groups' => [$threeFieldsInTwoGroups, 2],
            'source language missing' => [$singleField, 1, 'de'],
        ];
    }

    /**
     * https://phabricator.wikimedia.org/T219227
     */
    public function testMissingSourceLanguage(): void
    {
        $fieldset = new TranslationsFieldset([
            'translations' => [
                'span1' => ['fallback' => ['text' => 'Hello', 'data-parent' => 'text1']],
            ],
            'source_lang_code' => 'de',
            'target_lang_code' => 'eo',
        ]);
        /** @var FieldsetLayout[] $items */
        $items = $fieldset->getItems();
        static::assertCount(0, $items[0]->getItems());
    }

    /**
     * Tests for undefined index warning
     */
    public function testChildOnlyTranslations(): void
    {
        $svg = new SvgFile(dirname(__DIR__).'/data/child-only.svg');

        $fieldset = new TranslationsFieldset([
            'translations' => $svg->getInFileTranslations(),
            'source_lang_code' => 'fallback',
            'target_lang_code' => 'ru',
        ]);

        // One group
        static::assertCount(1, $fieldset->getItems());
    }
}
