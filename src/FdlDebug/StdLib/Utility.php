<?php
namespace FdlDebug\StdLib;

use FdlDebug\Bootstrap;

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
                } elseif (is_array($value) && (isset($a[$key]) && is_array($a[$key]))) {
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

    /**
     * Retrieve the last member key of a hashed/array
     * @return mixed
     */
    public static function arrayLastKey(array $array)
    {
        $array = array_keys($array);
        return array_pop($array);
    }

    /**
     * Return the xdebug trace file
     * @return string
     */
    public static function getXdebugTraceFile()
    {
        $config = Bootstrap::getConfigs();
        return $config['xdebug']['trace_output_dir'] . '/' . $config['xdebug']['trace_output_name'] . '.xt';
    }

    /**
     * Is XDebug enabled?
     * @param void
     * @return boolean
     */
    public static function isXdebugEnabled()
    {
        $config = Bootstrap::getConfigs();
        if (function_exists('xdebug_is_enabled')) {
            if (xdebug_is_enabled() === true && true == $config['xdebug_tracing_enabled']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Should the xdebug trace start?
     * @return boolean
     */
    public static function canXdebugTraceStart()
    {
        return (
            !empty($_COOKIE['XDEBUG_TRACE'])
            || !empty($_GET['XDEBUG_TRACE'])
            || !empty($_POST['XDEBUG_TRACE'])
            || !empty($_REQUEST['XDEBUG_TRACE'])
        );
    }

    /**
     * Is the session already started?
     * @return boolean
     */
    public static function isSessionStarted()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Starts a php session
     * @param void
     * @return null
     */
    public static function sessionStart()
    {
        if (static::isSessionStarted() === FALSE) {
            session_start();
        }
    }

    /**
     * Replaces the searched key with a new replacement key.
     * If the replacement key exist in the target array, the replacement will be ignored.
     *
     * @param  int|string $search  The key to search for
     * @param  int|string $replace The replacement for the searched key
     * @param  array $target       Target associative array
     */
    public static function arrayReplaceKey($search, $replace, array &$target)
    {
        $tempArray = array();
        foreach ($target as $key => $val) {
            if ($search === $key && array_key_exists($replace, $target) === false) {
                $tempArray[$replace] = $val;
            } else {
                $tempArray[$key] = $val;
            }
        }
        $target = $tempArray;
    }
}
