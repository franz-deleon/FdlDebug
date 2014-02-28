<?php
namespace FdlDebug;

class Bootstrap
{
    /**
     * Configs
     * @var array
     */
    protected static $configs = array();

    /**
     * Flag if the bootstrap has been run.
     * @var boolean
     */
    protected static $initialized = false;

    /**
     * Initialize the bootstrap
     * @param string $customConfigFile The custom config file to be initialized.
     *                                 Should be fully qualified file path.
     */
    public static function init($customConfigFile = null)
    {
        self::$configs = include __DIR__ . '/../../config/global.php';

        // give a chance to override the global configs
        if (file_exists(__DIR__ . '/../../config/local.php')) {
            $localConfig = include __DIR__ . '/../../config/local.php';
            self::$configs = self::merge(self::$configs, $localConfig);
        }
        if (null !== $customConfigFile && file_exists($customConfigFile)) {
            $customConfig = include $customConfigFile;
            if (is_array($customConfig)) {
                self::$configs = self::merge(self::$configs, $customConfig);
            } else {
                throw new \ErrorException(sprintf(
                    "File '%s' should return an array",
                    $customConfigFile
                ));
            }
        }

        // initialize the config
        include __DIR__ . '/../../config/config.init.php';

        self::$initialized = true;
    }

    /**
     * Return the configs
     * @return array
     */
    public static function getConfigs()
    {
        return self::$configs;
    }

    /**
     * Has the bootstrap been run?
     * @return boolean
     */
    public static function isInitialized()
    {
        return self::$initialized;
    }

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
    protected static function merge(array $a, array $b)
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
}
