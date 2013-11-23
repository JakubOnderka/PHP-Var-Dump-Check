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
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getShortFilePath()
    {
        return str_replace(getcwd(), '', $this->filePath);
    }
}