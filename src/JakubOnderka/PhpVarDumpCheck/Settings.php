<?php
namespace JakubOnderka\PhpVarDumpCheck;

class Settings
{
    const VAR_DUMP = 'var_dump',
        VAR_EXPORT = 'var_export',
        PRINT_R = 'print_r',
        ZEND_DEBUG_DUMP = 'Zend_Debug::dump',
        ZEND_DEBUG_DUMP_2 = '\Zend\Debug\Debug::dump',
        DEBUGGER_DUMP = 'Debugger::dump'; // Nette

    /**
     * If path contains directory, only file with these extensions are checked
     * @var array
     */
    public $extensions = array('php', 'phtml', 'php3', 'php4', 'php5');

    /**
     * Array of file or directories to check
     * @var array
     */
    public $paths = array();

    /**
     * Dont't check files or directories
     * @var array
     */
    public $excluded = array();

    /**
     * Use colors in console output
     * @var bool
     */
    public $colors = true;

    /**
     * @var array
     */
    public $functionsToCheck = array(self::VAR_DUMP, self::VAR_EXPORT, self::PRINT_R, self::ZEND_DEBUG_DUMP, self::ZEND_DEBUG_DUMP_2);


    /**
     * @param array $arguments
     * @return Settings
     * @throws \InvalidArgumentException
     */
    public static function parseArguments(array $arguments)
    {
        $arguments = new ArrayIterator(array_slice($arguments, 1));
        $setting = new self;

        foreach ($arguments as $argument) {
            if ($argument{0} !== '-') {
                $setting->paths[] = $argument;
            } else {
                switch ($argument) {
                    case '-e':
                        $setting->extensions = array_map('trim', explode(',', $arguments->getNext()));
                        break;

                    case '--exclude':
                        $setting->excluded[] = $arguments->getNext();
                        break;

                    case '--no-colors':
                        $setting->colors = false;
                        break;

                    default:
                        throw new Exception\InvalidArgument($argument);
                }
            }
        }

        return $setting;
    }

    /**
     * @param string $method
     * @return FunctionConditions
     */
    public function getFunctionCondition($method)
    {
        $functionConditions = array(
            self::VAR_DUMP => new FunctionConditions(2, false, false),
            self::PRINT_R => new FunctionConditions(2, false, false),
            self::VAR_EXPORT => new FunctionConditions(2, false, false),
            self::ZEND_DEBUG_DUMP => new FunctionConditions(3, true, true),
            self::DEBUGGER_DUMP => new FunctionConditions(2, false, false),
        );


        if (!isset($functionConditions[$method])) {
            return null;
        }

        return $functionConditions[$method];
    }
}