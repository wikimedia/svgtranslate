<?php
declare(strict_types = 1);

namespace App\Model;

/**
 * Static class that performs title-related operations
 */
final class Title
{
    /**
     * @param string $title
     * @return string
     */
    public static function removeNamespace(string $title): string
    {
        return preg_replace('/^file\s*:\s*/i', '', $title);
    }

    /**
     * @param string $title
     * @return string
     */
    public static function normalize(string $title): string
    {
        $title = self::removeNamespace($title);
        // Unicode-friendly ucfirst()
        $title = mb_strtoupper(mb_substr($title, 0, 1)).mb_substr($title, 1);
        $title = str_replace(' ', '_', $title);

        return $title;
    }
}
