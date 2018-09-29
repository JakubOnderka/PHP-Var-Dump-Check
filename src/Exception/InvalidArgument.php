<?php
namespace JakubOnderka\PhpVarDumpCheck\Exception;

class InvalidArgument extends Exception
{
    /** @var string */
    private $argument;

    public function __construct($argument)
    {
        $this->argument = $argument;
    }

    /**
     * @return string
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
