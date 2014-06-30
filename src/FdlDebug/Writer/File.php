<?php
namespace FdlDebug\Writer;

use FdlDebug\Front;
use FdlDebug\Bootstrap;

class File implements WriterInterface
{
    /**
     * Default file log path
     * @var string
     */
    protected $fileLogPath = "/tmp/fdldebug/log.txt";

    /**
     * Constructor.
     * Give a chance to override the writer_file_log_path within the config
     * @param void
     */
    public function __construct()
    {
        $configs = Bootstrap::getConfigs();
        if (!empty($configs['writer_file_log_path'])) {
            $this->fileLogPath = $configs['writer_file_log_path'];
        }
        if (!file_exists($this->fileLogPath)) {
             mkdir(dirname($this->fileLogPath), 0777, true);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Writer\WriterInterface::write()
     */
    public function write($content, $extra = null)
    {
        $debug = Front::i()->getDebug();
        $trace = $debug->findTraceKeyAndSlice($debug->getBackTrace(), 'function', '__call', 2);
        $file  = $trace[0]['file'];
        $line  = $trace[0]['line'];
        $time  = date('y-m-d h:i:s');

        switch ($extra['function']) {
            case 'printObject':
            case 'printGlobal':
            case 'printBackTrace':
            case 'printFiles':
            default:
                $string  = PHP_EOL . "******TOP ({$file}:{$line} at {$time})********" . PHP_EOL;
                $string .= print_r($content, true);
                $string .= PHP_EOL . "*****BOTTOM ({$file}:{$line} at {$time})******" . PHP_EOL;

                $handle  = fopen($this->fileLogPath, 'a+');
                if ($handle !== false) {
                    fwrite($handle, $string);
                    fclose($handle);
                } else {
                     throw new \ErrorException("Cannot create log file: '{$this->fileLogPath}'");
                }
        }
    }
}
