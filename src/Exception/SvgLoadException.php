<?php

declare(strict_types = 1);

namespace App\Exception;

use Exception;
use Throwable;

/**
 * For errors during SVG file loading and parsing
 * Accepts custom error messages or uses last system error from LibXML
 */
class SvgLoadException extends Exception
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        $error = error_get_last();
        if ($error && isset($error['message'])) {
            $message = $error['message'];
        }
        parent::__construct($message, 0, $previous);
    }
}
