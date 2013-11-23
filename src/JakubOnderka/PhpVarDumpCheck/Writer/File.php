<?php
namespace JakubOnderka\PhpVarDumpCheck\Writer;

class File implements Writer
{
    /** @var string */
    protected $logFile;

    /** @var string */
    protected $buffer;

    /**
     * @param string $logFile
     */
    public function __construct($logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * @param string $string
     */
    public function write($string)
    {
        $this->buffer .= $string;
    }

    public function __destruct()
    {
        file_put_contents($this->logFile, $this->buffer);
    }
}