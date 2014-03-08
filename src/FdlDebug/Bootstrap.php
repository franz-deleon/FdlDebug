<?php
namespace FdlDebug;

use FdlDebug\StdLib\Utility;

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
     *                                 Should be fully qualified file path or array.
     */
    public static function init($customConfigFile = null)
    {
        self::$configs = include __DIR__ . '/../../config/global.php';

        // give a chance to override the global configs
        if (file_exists(__DIR__ . '/../../config/local.php')) {
            $localConfig = include __DIR__ . '/../../config/local.php';
            self::$configs = Utility::merge(self::$configs, $localConfig);
        }
        if (null !== $customConfigFile) {
            if (is_string($customConfigFile) && file_exists($customConfigFile)) {
                $customConfig = include $customConfigFile;
            } elseif (is_array($customConfigFile)) {
                $customConfig = $customConfigFile;
            }

            if (isset($customConfig) && is_array($customConfig)) {
                self::$configs = Utility::merge(self::$configs, $customConfig);
            } else {
                throw new \ErrorException(sprintf(
                    "File '%s' should return an array",
                    $customConfigFile
                ));
            }
        }

        self::initXdebugTrace();

        self::$initialized = true;
    }

    /**
     * Initialize the xdebug trace configurations
     * @param void
     * @return null
     */
    public static function initXdebugTrace()
    {
        if (Utility::canXdebugTraceStart() && Utility::isXdebugEnabled()) {
            if (!file_exists(self::$configs['xdebug']['trace_output_dir'])) {
                mkdir(self::$configs['xdebug']['trace_output_dir'], 0777, true);
            }

            foreach (self::$configs['xdebug'] as $key => $val) {
                ini_set("xdebug.$key", $val);
            }
            xdebug_start_trace();
        }
    }

    /**
     * Return the configs
     * @return &array
     */
    public static function &getConfigs()
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
}
