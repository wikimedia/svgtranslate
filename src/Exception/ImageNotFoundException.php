<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageNotFoundException extends NotFoundHttpException
{
    public function __construct(string $fileName)
    {
        parent::__construct("Image $fileName not found on Commons");
    }
}
