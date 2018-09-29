<?php
namespace JakubOnderka\PhpVarDumpCheck\Writer;

interface Writer
{
    /**
     * @param string $string
     * @return void
     */
    public function write($string);
}