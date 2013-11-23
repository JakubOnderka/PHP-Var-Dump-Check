<?php
namespace JakubOnderka\PhpVarDumpCheck\Exception;

class NotExistsPath extends Exception
{
    /** @var string */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->message = "Path '$path' not found";
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}