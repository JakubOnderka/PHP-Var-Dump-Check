<?php
namespace JakubOnderka\PhpVarDumpCheck;

class Result
{
    /** @var string */
    protected $type;

    /** @var int */
    protected $lineNumber;

    /** @var bool */
    protected $sure;

    /** @var string */
    protected $filePath;

    /**
     * @param string $type
     * @param int $lineNumber
     * @param bool $sure
     */
    public function __construct($type, $lineNumber, $sure = true)
    {
        $this->type = $type;
        $this->lineNumber = $lineNumber;
        $this->sure = $sure;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @return bool
     */
    public function isSure()
    {
        return $this->sure;
    }

    /**
     * @return string
     */
    protected function getCodeSnippet()
    {
        if (!$this->filePath) {
            return '';
        }

        $line = $this->getLineNumber();
        $lines = file($this->filePath);

        if (!$lines) {
            return '';
        }

        $snippet = '';
        $lineStrlen = strlen($line + 2);
        $line -= 1; // because $lines array is indexed from zero

        for ($i = $line - 2; $i <= $line + 2; $i++) {
            if (isset($lines[$i])) {
                $snippet .= ($line === $i ? '  > ' : '    ');
                $snippet .= $this->stringWidth($i + 1, $lineStrlen) . '| ' . rtrim($lines[$i]) . PHP_EOL;
            }
        }

        return $snippet;
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

    /**
     * @return string
     */
    protected function getShortFilePath()
    {
        return str_replace(getcwd(), '', $this->filePath);
    }

    /**
     * @param bool $withCodeSnipped
     * @return string
     */
    public function getString($withCodeSnipped = true)
    {
        $string = "Forgotten dump {$this->getType()} found in {$this->getShortFilePath()}:{$this->getLineNumber()}" . PHP_EOL;

        if ($withCodeSnipped) {
            $string .= $this->getCodeSnippet();
        }


        return $string;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getString();
    }
}