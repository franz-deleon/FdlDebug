<?php
namespace FdlDebug;

class Autoloader
{
    private $dir;

    public function __construct($dir = null)
    {
        if (null === $dir) {
            $dir = dirname(__DIR__);
        }
        $this->dir = $dir;
    }

    public static function register($dir = null)
    {
        spl_autoload_register(array(new self($dir), 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    public function autoload($class)
    {
        if (0 !== strpos($class, __NAMESPACE__)) {
            return;
        }

        $file = $this->dir . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
