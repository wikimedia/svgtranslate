<?php
declare(strict_types = 1);

namespace App\Tests\OOUI;

use App\OOUI\TranslationsFieldset;
use PHPUnit\Framework\TestCase;

class TranslationsFieldsetTest extends TestCase
{

    /**
     * @dataProvider fieldsetGroupingProvider()
     */
    public function testFieldsetGrouping(array $translations, int $fieldsetCount): void
    {
        $fieldset = new TranslationsFieldset([
            'translations' => $translations,
            'source_lang_code' => 'fallback',
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
        ];
    }
}
