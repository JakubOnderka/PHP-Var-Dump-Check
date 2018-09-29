<?php
namespace JakubOnderka\PhpVarDumpCheck;

use Colors\Color;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;

class OutputColored extends Output
{
    /** @var Color */
    private $color;

    /** @var Highlighter */
    private $highlighter;

    public function __construct(
        Writer\Writer $writer,
        Color $color = null,
        Highlighter $highlighter = null
    ) {
        parent::__construct($writer);

        if (!$color && class_exists('\Colors\Color')) {
            $this->color = new Color();
        }

        if (!$highlighter && $this->color && class_exists('\JakubOnderka\PhpConsoleHighlighter\Highlighter')) {
            $this->highlighter = new Highlighter($this->color);
        }
    }

    public function error()
    {
        if ($this->color) {
            $this->writer->write($this->color->apply('bg_red', 'X'));
            $this->progress();
        } else {
            parent::error();
        }
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
        if ($this->highlighter) {
            return $this->highlighter->getCodeSnippet($fileContent, $lineNumber, $linesBefore, $linesAfter);
        } else {
            return parent::getCodeSnippet($fileContent, $lineNumber, $linesBefore, $linesAfter);
        }
    }
}