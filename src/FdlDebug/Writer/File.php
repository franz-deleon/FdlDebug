<?php
namespace FdlDebug\Writer;

use FdlDebug\Front;

class File implements WriterInterface
{
    protected $filePath = "/tmp/fdldebug/log.txt";

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
                $string  = PHP_EOL . "******************************TOP ({$file}:{$line} at {$time})**********************************" . PHP_EOL;
                $string .= print_r($content, true);
                $string .= PHP_EOL . "*****************************BOTTOM********************************" . PHP_EOL;

                $handle  = fopen($this->filePath, 'a+');
                fwrite($handle, $string);
                fclose($handle);
        }
    }
}
