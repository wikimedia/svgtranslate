<?php
declare(strict_types = 1);

/**
 * @file
 */

namespace App\Model\Svg;

class Transformations
{
    /**
     * Maps from the kind of <parameter name, value> combination used in
     * a property string to the kind of <attribute name, value> combination
     * used in an SVG file.
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

        // systemLanguage will be re-added separately.
        if ('systemLanguage' === $name) {
            return [false, false];
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
     * a property string. Also validates to prevent arbitrary input.
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
