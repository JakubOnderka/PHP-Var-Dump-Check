<?php
namespace JakubOnderka\PhpVarDumpCheck\Writer;

class Multiple implements Writer
{
    /** @var Writer[] */
    protected $writers;

    /**
     * @param Writer[] $writers
     */
    public function __construct(array $writers)
    {
        foreach ($writers as $writer) {
            $this->addWriter($writer);
        }
    }

    /**
     * @param Writer $writer
     */
    public function addWriter(Writer $writer)
    {
        $this->writers[] = $writer;
    }

    /**
     * @param string $string
     */
    public function write($string)
    {
        foreach ($this->writers as $writer) {
            $writer->write($string);
        }
    }
}