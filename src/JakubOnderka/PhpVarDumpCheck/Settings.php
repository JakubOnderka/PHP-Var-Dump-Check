<?php
namespace JakubOnderka\PhpVarDumpCheck;

class Settings
{
    const VAR_DUMP = 'var_dump',
        VAR_EXPORT = 'var_export',
        PRINT_R = 'print_r',

        ZEND_DEBUG_DUMP = 'Zend_Debug::dump',
        ZEND_DEBUG_DUMP_2 = '\Zend\Debug\Debug::dump',

        DEBUGGER_DUMP = 'Debugger::dump', // Nette, Tracy

        LADYBUG_DUMP = 'ladybug_dump',
        LADYBUG_DUMP_DIE =  'ladybug_dump_die',
        LADYBUG_DUMP_SHORTCUT = 'ld',
        LADYBUG_DUMP_DIE_SHORTCUT = 'ldd';

    /**
     * If path contains directory, only file with these extensions are checked
     * @var array
     */
    public $extensions = array('php', 'php3', 'php4', 'php5', 'phtml');

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
    public $functionsToCheck = array(
        self::VAR_DUMP,
        self::VAR_EXPORT,
        self::PRINT_R,
    );

    /**
     * @param array $arguments
     * @return Settings
     * @throws Exception\InvalidArgument
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
                    case '--extensions':
                        $setting->extensions = array_map('trim', explode(',', $arguments->getNext()));
                        break;

                    case '--exclude':
                        $setting->excluded[] = $arguments->getNext();
                        break;

                    case '--no-colors':
                        $setting->colors = false;
                        break;

                    case '--tracy':
                        $setting->functionsToCheck[] = self::DEBUGGER_DUMP;
                        break;

                    case '--zend':
                        $setting->functionsToCheck[] = self::ZEND_DEBUG_DUMP;
                        $setting->functionsToCheck[] = self::ZEND_DEBUG_DUMP_2;
                        break;

                    case '--ladybug':
                        $setting->functionsToCheck[] = self::LADYBUG_DUMP;
                        $setting->functionsToCheck[] = self::LADYBUG_DUMP_DIE;
                        $setting->functionsToCheck[] = self::LADYBUG_DUMP_SHORTCUT;
                        $setting->functionsToCheck[] = self::LADYBUG_DUMP_DIE_SHORTCUT;
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