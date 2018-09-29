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
        DEBUGGER_DUMP_SHORTCUT = 'dump', // shortcut for Debugger::dump
        DEBUGGER_BARDUMP = 'Debugger::barDump', // Nette, Tracy
        DEBUGGER_BARDUMP_SHORTCUT = 'bdump', // shortcut for Debugger::dump

        LADYBUG_DUMP = 'ladybug_dump',
        LADYBUG_DUMP_DIE =  'ladybug_dump_die',
        LADYBUG_DUMP_SHORTCUT = 'ld',
        LADYBUG_DUMP_DIE_SHORTCUT = 'ldd',

        SYMFONY_VARDUMPER_HANDLER = 'VarDumper::setHandler',
        SYMFONY_VARDUMPER_DUMP = 'VarDumper::dump',
        SYMFONY_VARDUMPER_DUMP_SHORTCUT = 'dump',

        LARAVEL_DUMP_DD = 'dd',

        DOCTRINE_DUMP = 'Doctrine::dump',
        DOCTRINE_DUMP_2 = '\Doctrine\Common\Util\Debug::dump';

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
                        $setting->functionsToCheck[] = self::DEBUGGER_DUMP_SHORTCUT;
                        $setting->functionsToCheck[] = self::DEBUGGER_BARDUMP;
                        $setting->functionsToCheck[] = self::DEBUGGER_BARDUMP_SHORTCUT;
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

                    case '--symfony':
                        $setting->functionsToCheck[] = self::SYMFONY_VARDUMPER_DUMP;
                        $setting->functionsToCheck[] = self::SYMFONY_VARDUMPER_DUMP_SHORTCUT;
                        $setting->functionsToCheck[] = self::SYMFONY_VARDUMPER_HANDLER;
                        break;

                    case '--laravel':
                        $setting->functionsToCheck[] = self::LARAVEL_DUMP_DD;
                        break;

                    case '--doctrine':
                        $setting->functionsToCheck[] = self::DOCTRINE_DUMP;
                        $setting->functionsToCheck[] = self::DOCTRINE_DUMP_2;
                        break;

                    default:
                        throw new Exception\InvalidArgument($argument);
                }
            }
        }
        $setting->functionsToCheck = array_unique($setting->functionsToCheck);
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
            self::DOCTRINE_DUMP => new FunctionConditions(2, false, false),
        );


        if (!isset($functionConditions[$method])) {
            return null;
        }

        return $functionConditions[$method];
    }
}
