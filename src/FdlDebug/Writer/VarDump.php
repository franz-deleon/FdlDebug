<?php
namespace FdlDebug\Writer;

class VarDump implements WriterInterface
{
    public function write($content, $extra = null)
    {
        var_dump($content);
    }
}
