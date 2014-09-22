<?php
namespace FdlDebug;

use FdlDebug\StdLib\Utility;

class Bootstrap
{
    /**
     * @var string
     */
    const FUNCTIONS_FILENAME = 'Functions.php';

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
     * @param string $customConfigFileOr Array The custom config file to be initialized.
     *                                   Should be fully qualified file path or array.
     */
    public static function init($customConfigFileOrArray = null)
    {
        self::$configs = include_once __DIR__ . '/../../config/global.php';

        // give a chance to override the global configs
        if (file_exists(__DIR__ . '/../../config/local.php')) {
            $localConfig = include_once __DIR__ . '/../../config/local.php';
            self::$configs = Utility::merge(self::$configs, $localConfig);
        }
        if (null !== $customConfigFileOrArray) {
            if (is_string($customConfigFileOrArray) && file_exists($customConfigFileOrArray)) {
                $customConfig = include_once $customConfigFileOrArray;
            } elseif (is_array($customConfigFileOrArray)) {
                $customConfig = $customConfigFileOrArray;
            }

            if (isset($customConfig) && is_array($customConfig)) {
                self::$configs = Utility::merge(self::$configs, $customConfig);
            } else {
                throw new \ErrorException(sprintf(
                    "File '%s' should return an array",
                    $customConfigFileOrArray
                ));
            }
        }

        // Include the procedural php functions
        include_once __DIR__ . '/../../' . self::FUNCTIONS_FILENAME;

        // Initialize xdebug
        self::initXdebugTrace();

        // Set the flag
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
    public static function getConfigs()
    {
        return self::$configs;
    }

    /**
     * Set a config
     * @param string $key
     * @param mixed  $value
     */
    public static function setConfigs($key, $value)
    {
        $toArray = array();
        $toArray[$key] = $value;
        self::$configs = Utility::merge(self::$configs, $toArray);
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
