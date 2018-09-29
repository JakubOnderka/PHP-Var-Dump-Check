<?php
namespace JakubOnderka\PhpVarDumpCheck;

class Output
{
    /** @var int */
    public $filesPerLine = 60;

    /** @var int */
    protected $checkedFiles;

    /** @var int */
    protected $totalFileCount;

    /** @var Writer\Writer */
    protected $writer;

    /**
     * @param Writer\Writer $writer
     */
    public function __construct(Writer\Writer $writer)
    {
        $this->writer = $writer ?: new Writer\Console;
    }

    public function ok()
    {
        $this->writer->write('.');
        $this->progress();
    }

    public function error()
    {
        $this->writer->write('X');
        $this->progress();
    }

    public function fail()
    {
        $this->writer->write('-');
        $this->progress();
    }

    /**
     * @param string|null $line
     */
    public function writeLine($line = null)
    {
        $this->writer->write($line . PHP_EOL);
    }

    /**
     * @param int $count
     */
    public function writeNewLine($count = 1)
    {
        $this->writer->write(str_repeat(PHP_EOL, $count));
    }

    /**
     * @param int $count
     */
    public function setTotalFileCount($count)
    {
        $this->totalFileCount = $count;
    }

    /**
     * @param Result $result
     * @param bool $withCodeSnippet
     */
    public function writeResult(Result $result, $withCodeSnippet = true)
    {
        $string = "Forgotten dump '{$result->getType()}' found in {$result->getShortFilePath()}:{$result->getLineNumber()}" . PHP_EOL;

        if ($withCodeSnippet) {
            $string .= $this->getCodeSnippet(file_get_contents($result->getFilePath()), $result->getLineNumber());
        }

        $this->writer->write($string);
    }

    /**
     * @param string $fileContent
     * @param int $lineNumber
     * @param int $linesBefore
     * @param int $linesAfter
     * @return string
     */
    protected function getCodeSnippet($fileContent, $lineNumber, $linesBefore = 2, $linesAfter = 2)
    {
        $lines = explode("\n", $fileContent);

        $offset = $lineNumber - $linesBefore - 1;
        $offset = max($offset, 0);
        $length = $linesAfter + $linesBefore + 1;
        $lines = array_slice($lines, $offset, $length, $preserveKeys = true);

        end($lines);
        $lineStrlen = strlen(key($lines) + 1);

        $snippet = '';
        foreach ($lines as $i => $line) {
            $snippet .= ($lineNumber === $i + 1 ? '  > ' : '    ');
            $snippet .= str_pad($i + 1, $lineStrlen, ' ', STR_PAD_LEFT) . '| ' . rtrim($line) . PHP_EOL;
        }

        return $snippet;
    }

    protected function progress()
    {
        if (++$this->checkedFiles % $this->filesPerLine === 0) {
            if ($this->totalFileCount != 0) { // !=
                $percent = round($this->checkedFiles / $this->totalFileCount * 100);
                $current = $this->stringWidth($this->checkedFiles, strlen($this->totalFileCount));
                $this->writeLine(" $current/$this->totalFileCount ($percent %)");
            }
        }
    }

    /**
     * @param string $input
     * @param int $width
     * @return string
     */
    protected function stringWidth($input, $width = 3)
    {
        $multiplier = $width - strlen($input);
        return str_repeat(' ', $multiplier > 0 ? $multiplier : 0) . $input;
    }
}