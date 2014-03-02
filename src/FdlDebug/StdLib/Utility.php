<?php
namespace FdlDebug\StdLib;

abstract class Utility
{
    /**
     * Borrowed from ZF2's Stdlib::ArrayUtils
     *
     * @link      http://github.com/zendframework/zf2 for the canonical source repository
     * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     *
     * @param array $a
     * @param array $b
     * @return string
     */
    public static function merge(array $a, array $b)
    {
        foreach ($b as $key => $value) {
            if (isset($key, $a)) {
                if (is_int($key)) {
                    $a[] = $value;
                } elseif (is_array($value) && is_array($a[$key])) {
                    $a[$key] = static::merge($a[$key], $value);
                } else {
                    $a[$key] = $value;
                }
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }

    /**
     * Convert an undescore separated string to camelcased
     *
     * @param string $string
     * @return string
     */
    public static function underscoreToCamelcase($string)
    {
        $string = explode('_', $string);
        array_walk($string, function (&$item, $key) {
            if ($key > 0) {
                $item = ucfirst($item);
            }
            return $item;
        });
        return ucfirst(implode('', $string));
    }
}