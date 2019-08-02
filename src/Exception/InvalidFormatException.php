<?php
declare(strict_types = 1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class InvalidFormatException extends UnsupportedMediaTypeHttpException
{
    public function __construct(string $format)
    {
        parent::__construct("Files of type '$format' are not supported");
    }
}
