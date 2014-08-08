<?php
namespace FdlDebug\Writer;

use FdlDebug\Front;
use FdlDebug\Bootstrap;

class File extends AbstractWriter implements WriterInterface
{
    /**
     * Default file log path
     * @var string
     */
    protected $fileLogDir = "/tmp/fdldebug";

    /**
     * Default file log name
     * @var string
     */
    protected $fileLogName = "log.txt";

    /**
     * Log File name
     * @var string
     */
    protected $fileLogFileName;

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * The time lapse until we should add more spacing
     * @var int The value in seconds
     */
    protected $spaceTimeBuffer = 2;

    /**
     * How many spaces to add relative to spaceTimeBuffer
     * @var int The number of spaces
     */
    protected $spacerCount = 5;

    /**
     * Constructor.
     * Give a chance to override the writer_file_log_path within the config
     * @param void
     */
    public function __construct()
    {
        $configs = Bootstrap::getConfigs();
        if (!empty($configs['writer_file_log_dir'])) {
            $this->fileLogDir = rtrim($configs['writer_file_log_dir'], ' /');
        }
        if (!empty($configs['writer_file_log_name'])) {
            $this->fileLogName = $configs['writer_file_log_name'];
        }

        $this->fileLogFileName = $this->fileLogDir . '/' . $this->fileLogName;

        if (!file_exists($this->fileLogDir)) {
            @mkdir(dirname($this->fileLogDir));
        }

        $this->fileHandle = fopen($this->fileLogFileName, 'a+');
        @chmod($this->fileLogFileName, 0777);
    }

    /**
     * Close the file handle
     */
    function __destruct()
    {
        fclose($this->fileHandle);
    }

    /**
     * (non-PHPdoc)
     * @see \FdlDebug\Writer\WriterInterface::write()
     */
    public function write($content, $extra = null)
    {
        if ($this->getRunWrite() == false) {
            return $content;
        }

        $time  = date('Y-m-d h:i:s');
        $host  = gethostname();
        $debug = Front::i()->getDebug();
        $file  = $debug->getFile();
        $line  = $debug->getLine();

        switch ($extra['function']) {
            case 'printObject':
            case 'printGlobal':
            case 'printBackTrace':
            case 'printFiles':
            default:
                $retval  = "";
                $retval  .= ($this->addSpacer()) ? str_repeat(PHP_EOL, $this->spacerCount) : "";
                $retval .= PHP_EOL . "******START ({$host}:{$file}:{$line} at {$time})********" . PHP_EOL;
                $retval .= print_r($content, true);
                $retval .= PHP_EOL . "******END ({$host}:{$file}:{$line} at {$time})**********" . PHP_EOL;

                if ($this->fileHandle !== false) {
                    fwrite($this->fileHandle, $retval);
                } else {
                    $posix = posix_getpwuid(posix_getuid());
                    throw new \ErrorException("Cannot create log file: '{$this->fileLogFileName}' for user: {$posix['name']}");
                }
        }
    }

    /**
     * Should we add more spacer?
     * This is usefull for using the linux tail look at the end of file
     * @return bool
     */
    protected function addSpacer()
    {
        if ($this->fileHandle && filesize($this->fileLogFileName) > 0) {
            $pos  = -2;
            $text = '';
            while ($text != "\n") {
                fseek($this->fileHandle, $pos, SEEK_END);
                $text = fgetc($this->fileHandle);
                $pos  = $pos - 1;
            }

            preg_match('~(?P<date>[0-9]{4}-[0-9]{2}-[0-9]{2}) (?P<time>[0-9]{2}:[0-9]{2}:[0-9]{2})~', fgets($this->fileHandle), $matches);

            if (!empty($matches['date']) && !empty($matches['time'])) {
                $timeStart = strtotime("{$matches['date']} {$matches['time']}");
                $timeEnd   = time();
                $secondLaps = $timeEnd - $timeStart;

                if ($secondLaps > $this->spaceTimeBuffer) {
                    return true;
                }
            }
        }
        return false;
    }
}
