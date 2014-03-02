<?php
namespace FdlDebug;

use FdlDebug\Writer\WriterInterface;

interface DebugInterface
{
    public function setWriter(WriterInterface $writer);
}
