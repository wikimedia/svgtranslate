<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class ImageNotFoundException extends Exception
{
    public function __construct(string $fileName)
    {
        parent::__construct("Image $fileName not found on Commons");
    }
}
