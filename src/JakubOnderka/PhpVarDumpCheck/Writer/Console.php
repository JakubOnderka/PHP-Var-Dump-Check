<?php
namespace JakubOnderka\PhpVarDumpCheck\Writer;

class Console implements Writer
{
    /**
     * @param string $string
     */
    public function write($string)
    {
        echo $string;
    }
}