<?php
namespace FdlDebug\Writer;

require_once realpath(__DIR__ . '/../../../lib/FirePHP/FirePHP.class.php');

use \FirePHP;

class FirePHPWriterChrome extends AbstractWriter implements WriterInterface
{
    public function write($content, $extra = null)
    {
        // return the raw content
        if ($this->getRunWrite() == false) {
            return $content;
        }

        $firephp = FirePHP::getInstance(true);

        switch ($extra['function']) {
            case 'printObject':
                break;
            case 'printGlobal':
                if (!empty($extra['type'])) {
                    foreach (array_chunk($content, 20, true) as $chunkIndex => $chunkArray) {
                        $return = array(array('key', 'value'));
                        foreach ($chunkArray as $key => $val) {
                            $return[] = array($key, $val);
                        }
                        $firephp->fb($return, "Global {$extra['type']}" . ($chunkIndex > 0 ? ' cont...' : ''), FirePHP::TABLE);
                    }
                } else {
                    foreach ($content as $globalType => $globalContent) {
                        if (!empty($globalContent)) {
                            foreach (array_chunk($globalContent, 15, true) as $chunkIndex => $chunkArray) {
                                $return = array(array('key', 'value'));
                                foreach ($chunkArray as $key => $val) {
                                    $return[] = array($key, $val);
                                }
                                $firephp->fb($return, "Global {$globalType}" . ($chunkIndex > 0 ? ' cont...' : ''), FirePHP::TABLE);
                            }
                        }
                    }
                }
                break;
            case 'printBackTrace':
                $last = array_pop($content);
                $content[] = $last;
                array_unshift($content, array_keys($last));
                $firephp->fb($content, "Back Trace", FirePHP::TABLE);
                break;
            case 'printFiles':
                array_unshift($content, array('order', 'file'));
                $firephp->fb($content, "Files", FirePHP::TABLE);
                break;
            default:
                $firephp->fb($content);
        }
    }
}