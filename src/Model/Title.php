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
        return preg_replace('/.*file\s*:\s*/i', '', $title);
    }

    /**
     * Get the 'DB key' form of a title (underscores not spaces, and uppercase first character).
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

    /**
     * Get the 'text' form of a title (spaces not underscores).
     * @param string $title
     * @return string
     */
    public static function text(string $title): string
    {
        return str_replace('_', ' ', static::normalize($title));
    }
}
