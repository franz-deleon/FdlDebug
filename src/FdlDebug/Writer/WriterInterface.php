<?php
namespace FdlDebug\Writer;

interface WriterInterface
{
    /**
     * Consumes a content and extra parameter
     * @param mixed              $content The main content to be written
     * @param mixed|array|string $extra   Extra parameters that are needed
     *                                    for class implementations
     */
    public function write($content, $extra = null);
}