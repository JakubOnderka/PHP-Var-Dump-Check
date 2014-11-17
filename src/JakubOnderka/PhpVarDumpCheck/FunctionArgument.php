<?php
namespace JakubOnderka\PhpVarDumpCheck;

class FunctionArgument
{
    /** @var array */
    protected $tokens = array();

    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @return array
     */
    public function isTrue()
    {
        $sure = true;
        $prediction = null;

        for ($i = 0; $i < count($this->tokens); $i++) {
            if (is_array($this->tokens[$i])) {
                list($tokenType, $token) = $this->tokens[$i];
                $token = strtolower($token);
                if ($tokenType === T_COMMENT) {
                    continue;
                } else if ($tokenType === T_STRING && in_array($token, array('true', 'false', 'null'))) {
                    if ($token === 'true') {
                        $prediction = true;
                    } else {
                        $prediction = false;
                    }
                } else if ($tokenType === T_LNUMBER) {
                    $prediction = $token !== '0';
                } else if ($tokenType === T_DNUMBER) {
                    $prediction = $token !== '0.0';
                } else if ($tokenType === T_VARIABLE) {
                    $skipTo = $this->skipVariableAssign($i + 1);
                    if ($skipTo === false) {
                       $sure = false;
                    } else {
                        $i = $skipTo;
                    }
                } else {
                    $sure = false;
                }
            } else {
                $sure = false;
            }
        }

        return array($prediction, $sure);
    }

    /**
     * @param int $from
     * @return bool
     */
    protected function skipVariableAssign($from)
    {
        for ($i = $from; $i < count($this->tokens); $i++) {
            if (is_array($this->tokens[$i])) {
                list($tokenType) = $this->tokens[$i];
                if ($tokenType === T_COMMENT) {
                    continue;
                }
            } else if ($this->tokens[$i] === '=') {
                return $i;
            } else {
                return false;
            }
        }

        return false;
    }
}