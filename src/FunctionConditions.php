<?php
namespace JakubOnderka\PhpVarDumpCheck;

class FunctionConditions
{
    /** @var int */
    protected $argumentNumber;

    /** @var boolean */
    protected $mustBe;

    /** @var boolean */
    protected $default;

    /**
     * @param int $argumentNumber
     * @param boolean $mustBe
     * @param boolean $default
     */
    public function __construct($argumentNumber, $mustBe, $default)
    {
        $this->argumentNumber = $argumentNumber;
        $this->mustBe = $mustBe;
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getArgumentNumber()
    {
        return $this->argumentNumber;
    }

    /**
     * @return boolean
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return boolean
     */
    public function getMustBe()
    {
        return $this->mustBe;
    }
}