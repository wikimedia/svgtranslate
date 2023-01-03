<?php

declare(strict_types = 1);

namespace App\Exception;

use Exception;
use LibXMLError;
use Throwable;

/**
 * For errors during SVG file loading and parsing
 * Accepts custom error messages or uses last system error from LibXML
 */
class SvgLoadException extends Exception
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        if ('' === $message) {
            $errors = libxml_get_errors();
            $message = implode("\n", array_map(function (LibXMLError $e) {
                return trim($e->message).' in '.basename($e->file)." line {$e->line}";
            }, $errors));
        }
        parent::__construct($message, 0, $previous);
    }
}
