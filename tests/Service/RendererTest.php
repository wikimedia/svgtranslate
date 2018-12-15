<?php
declare(strict_types = 1);

namespace App\Tests\Service;

use App\Service\Renderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RendererTest extends TestCase
{

    public function testInvalidCommand() : void
    {
        $renderer = new Renderer('foo');
        static::expectException(ProcessFailedException::class);
        static::expectExceptionMessage('foo: not found');
        $renderer->render('foo.svg', 'fr');
    }
}
