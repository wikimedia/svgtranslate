<?php
declare(strict_types = 1);

namespace App\Tests\Model;

use App\Model\Title;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    /**
     * @covers Title::normalize()
     * @covers Title::removeNamespace()
     * @dataProvider provideNormalize
     *
     * @param string $title
     * @param string $expected
     */
    public function testNormalize(string $title, string $expected): void
    {
        self::assertEquals($expected, Title::normalize($title));
    }

    /**
     * @return string[]
     */
    public function provideNormalize(): array
    {
        return [
            ['foo bar.svg', 'Foo_bar.svg'],
            ['file:Тест.svg', 'Тест.svg'],
            ['file:тест_123.svg', 'Тест_123.svg'],
            ['Тест.svg', 'Тест.svg'],
            ['тест_123.svg', 'Тест_123.svg'],
        ];
    }
}
