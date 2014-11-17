<?php
namespace JakubOnderka\PhpVarDumpCheck;

class Checker
{
    /** @var Settings */
    protected $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $content
     * @return Result[]
     */
    public function check($content)
    {
        $tokens = $this->tokenize($content);
        $results = $this->checkForDumps($tokens);

        return $results;
    }

    /**
     * @param array $tokens
     * @return Result[]
     */
    protected function checkForDumps(array $tokens)
    {
        $results = array();

        $functionsToCheck = $this->prepareFunctionCheck($this->settings->functionsToCheck);

        foreach ($tokens as $key => $token) {
            if (
                is_array($token) &&
                (($token[0] === T_STRING && isset($functionsToCheck[$token[1]])) || isset($functionsToCheck[$token[0]]))
            ) {
                if (
                    !$this->checkPrevTokens($tokens, $key) ||
                    !$this->checkNextTokens($tokens, $functionsToCheck, $key)
                ) {
                    continue;
                }

                $conditions = $this->settings->getFunctionCondition($token[1]);
                list($prediction, $sure) = $this->checkFunctionCall($tokens, $key, $conditions);

                if ($prediction === true || $sure === false) {
                    $results[] = new Result($token[1], $token[2], $sure);
                }
            }
        }

        return $results;
    }

    /**
     * @param array $tokens
     * @param int $key
     * @return bool
     */
    protected function checkPrevTokens(array $tokens, $key)
    {
        $prevToken = $tokens[$key - 1];

        if (is_array($prevToken)) {
            return $prevToken[0] === T_OPEN_TAG || $prevToken[0] === T_OPEN_TAG_WITH_ECHO || $prevToken[0] === T_NS_SEPARATOR;
        } else {
            return $prevToken === '{' || $prevToken === ';' || $prevToken === '}';
        }
    }

    /**
     * @param array $tokens
     * @param array $functionsToCheck
     * @param int $key
     * @return bool
     */
    protected function checkNextTokens(array $tokens, array $functionsToCheck, $key)
    {
        $next = &$functionsToCheck;

        do {
            $currentToken = $tokens[$key++];

            if (!is_array($currentToken)) {
                return false;
            }

            if ($currentToken[0] === T_STRING && isset($next[$currentToken[1]])) {
                $next = &$next[$currentToken[1]];
            } else if (isset($next[$currentToken[0]])) {
                $next = &$next[$currentToken[0]];
            } else {
                return false;
            }

            if (empty($next)) {
                return true;
            }
        } while (true);
    }

    /**
     * @param array $tokens
     * @param int $from
     * @param FunctionConditions|null $conditions
     * @return array
     */
    protected function checkFunctionCall(array $tokens, $from, FunctionConditions $conditions = null)
    {
        /** @var FunctionArgument[] $arguments */
        list($ok, $arguments) = $this->checkIsFunctionCall($tokens, $from);

        if (!$ok) {
            return array(false, true);
        }

        if ($conditions) {
            if (isset($arguments[$conditions->getArgumentNumber() - 1])) {
                list($isTrue, $sure) = $arguments[$conditions->getArgumentNumber() - 1]->isTrue();
                return array($isTrue === $conditions->getMustBe(), $sure);
            } else {
                return $conditions->getMustBe() === $conditions->getDefault() ? array(true, true) : array(false, true);
            }
        }

        return array(true, true);
    }

    /**
     * @param array $tokens
     * @param int $from
     * @return array
     */
    protected function checkIsFunctionCall(array $tokens, $from)
    {
        $arguments = array();

        $count = 0;
        $argumentFrom = 0;

        for ($i = $from + 1; $i < count($tokens); $i++) {
            if ($tokens[$i] === '(') {
                $count++;
                if ($count === 1) {
                    $argumentFrom = $i + 1;
                }
            } else if ($tokens[$i] === ')') {
                if (--$count === 0) {
                    $arguments[] = new FunctionArgument(array_slice($tokens, $argumentFrom, $i - $argumentFrom));
                    return array(true, $arguments);
                }
            }  else if ($tokens[$i] === ',' && $count === 1) {
                $arguments[] = new FunctionArgument(array_slice($tokens, $argumentFrom, $i - $argumentFrom));
                $argumentFrom = $i + 1;
            }
        }

        return array(false, $arguments);
    }

    /**
     * @param string $content
     * @return array
     */
    protected function tokenize($content)
    {
        $tokens = token_get_all($content);
        $tokens = array_values(array_filter($tokens, function ($token) {
            return !is_array($token) || $token[0] !== T_WHITESPACE;
        }));

        return $tokens;
    }

    /**
     * @param array $functionsToCheck
     * @return array
     */
    protected function prepareFunctionCheck(array $functionsToCheck)
    {
        $output = array();

        foreach ($functionsToCheck as $function) {
            $namespaces = explode('\\', $function);

            $next = &$output;

            foreach ($namespaces as $key => $namespace) {
                if (strpos($namespace, '::') !== false) {
                    list($first, $second) = explode('::', $namespace);

                    if (!isset($next[$first])) {
                        $next[$first] = array();
                    }
                    $next = &$next[$first];

                    if (!isset($next[T_DOUBLE_COLON])) {
                        $next[T_DOUBLE_COLON] = array();
                    }
                    $next = &$next[T_DOUBLE_COLON];

                    if (!isset($next[$second])) {
                        $next[$second] = array();
                    }
                    $next = &$next[$second];
                } else if (!empty($namespace)) {
                    if (!isset($next[$namespace])) {
                        $next[$namespace] = array();
                    }
                    $next = &$next[$namespace];

                    if (isset($namespaces[$key + 1])) {
                        if (!isset($next[T_NS_SEPARATOR])) {
                            $next[T_NS_SEPARATOR] = array();
                        }
                        $next = &$next[T_NS_SEPARATOR];
                    }
                } else {
                    if (!isset($next[T_NS_SEPARATOR])) {
                        $next[T_NS_SEPARATOR] = array();
                    }
                    $next = &$next[T_NS_SEPARATOR];
                }
            }
        }

        return $output;
    }
}


