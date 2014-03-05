<?php
namespace FdlDebug\Writer;

class GenericOutput extends AbstractGenericOutput implements WriterInterface
{
    public function write($content, $extra = null)
    {
        $outputter = $this->getOutputter();
        if ($outputter === 'echo' || $outputter === 'print') {
            echo $content;
        } else {
            $outputter($content);
        }
    }
}
