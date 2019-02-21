<?php
declare(strict_types = 1);

/**
 * @file
 */

namespace App\Model\Svg;

class Transformations
{
    private const DEFAULT_PROPERTIES = [
        'x'         => '', 'y' => '', 'font-family' => 'other',
        'font-size' => '', 'units' => 'other', 'color' => '',
        'underline' => '', 'italic' => '', 'bold' => '',
    ];

    private const OPTIONAL_PROPERTIES = [
        'id',
        'data-children',
        'xml:space',
        'sodipodi:role',
        'sodipodi:linespacing',
    ];

    /**
     * Maps from the kind of <parameter name,value> combination used in
     * a property string to the kind of <attribute name, value> combination
     * used in an SVG file. Also validates to prevent arbitary input.
     *
     * @see self::mapFromAttribute()
     * @param string $name Parameter name (e.g. color, bold)
     * @param string $value Parameter value (e.g. black, yes)
     * @return string[]|false[] Numerical array, [0] = attribute name, [1] = value
     */
    public static function mapToAttribute(string $name, string $value): array
    {
        $name = trim($name);
        $value = trim($value);

        $supported = array_merge(
            array_keys(self::DEFAULT_PROPERTIES),
            self::OPTIONAL_PROPERTIES
        );

        if (!in_array($name, $supported)) {
            // Quietly drop: injection attempt?
            return [ false, false ];
        }
        switch ($name) {
            case 'bold':
                $name = 'font-weight';
                $value = 'yes' === $value ? 'bold' : 'normal';
                break;
            case 'italic':
                $name = 'font-style';
                $value = 'yes' === $value ? 'italic' : 'normal';
                break;
            case 'underline':
                $name = 'text-decoration';
                $value = 'yes' === $value ? 'underline' : 'normal';
                break;
            case 'color':
                $name = 'fill';
                break;
        }
        if ('' === $value) {
            $value = false;
        }
        return [ $name, $value ];
    }

    /**
     * Maps from the kind of <attribute name, value> combination used in
     * an SVG file to the kind of <parameter name,value> combination used in
     * a property string. Also validates to prevent arbitary input.
     *
     * @see self::mapToAttribute()
     * @param string $name Attribute name (e.g. fill, font-weight)
     * @param string $value Attribute value (e.g. black, bold)
     * @return string[] Numerical array, [0] = parameter name, [1] = parameter value
     */
    public static function mapFromAttribute(string $name, string $value): array
    {
        $name = trim($name);
        $value = trim($value);

        $supported = array_merge(
            [
                'x', 'y', 'font-size', 'font-weight', 'font-style',
                'text-decoration', 'font-family', 'fill', 'style',
                'systemLanguage',
            ],
            self::OPTIONAL_PROPERTIES
        );
        if (!in_array($name, $supported)) {
            // Not editable, so not suitable for extraction
            return [ false, false ];
        }

        switch ($name) {
            case 'font-weight':
                $name = 'bold';
                $value = 'bold' === $value ? 'yes' : 'no';
                break;
            case 'font-style':
                $name = 'italic';
                $value = 'italic' === $value ? 'yes' : 'no';
                break;
            case 'text-decoration':
                $name = 'underline';
                $value = 'underline' === $value ? 'yes' : 'no';
                break;
            case 'font-family':
                $map = [ 'Sans' => 'sans-serif' ];
                $value = $map[$value] ?? $value;
                break;
            case 'fill':
                $name = 'color';
                break;
        }
        if ('' == $value) {
            // Drop empty attributes altogether
            $value = false;
        }
        return [ $name, $value ];
    }
}
