<?php
namespace FdlDebug\Writer;

class GenericOutput extends AbstractGenericOutput implements WriterInterface
{
    public function write($content, $extra = null)
    {
        $outputter = $this->getOutputter();
        if ($outputter === 'echo' || $outputter === 'print') {
            echo $content;
        } elseif (
            ($outputter === 'var_export' || $outputter === 'print_r')
            && $this->isReturn()
        ) {
            return trim($outputter($content, true), " '");
        } else {
            $outputter($content);
        }
    }
}
