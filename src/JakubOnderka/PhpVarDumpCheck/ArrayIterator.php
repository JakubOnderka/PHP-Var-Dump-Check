<?php
namespace JakubOnderka\PhpVarDumpCheck;

class ArrayIterator extends \ArrayIterator
{
    public function getNext()
    {
        $this->next();
        return $this->current();
    }
}