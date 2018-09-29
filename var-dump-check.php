<?php
use JakubOnderka\PhpVarDumpCheck;

const VERSION = '0.3';

const SUCCESS = 0,
    WITH_ERRORS = 1,
    FAILED = 254; // 255 code is reserved to PHP itself

if (PHP_VERSION < '5.4.0') {
    fwrite(STDERR,"PHP Var Dump Check require PHP 5.4.0 and newer");
    die(FAILED);
}

function showOptions() {
?>
Options:
    --tracy       Enable support for Tracy (Debugger::dump)
    --zend        Enable support for Zend (Zend_Debug::dump and \Zend\Debug\Debug::dump)
    --ladybug     Enable support for Ladybug (ladybug_dump, ladybug_dump_die, ld, ldd)
    --symfony     Enable support for Symfony2 (dump, VarDumper::dump, VarDumper::setHandler)
    --doctrine    Enable support for Doctrine (Doctrine::dump, \Doctrine\Common\Util\Debug::dump)
    --extensions  Check only files with selected extensions separated by comma
                  (default: php, php3, php4, php5, phtml)
    --exclude     Exclude directory. If you want exclude multiple directory, use
                  multiple exclude parameters.
    --no-colors   Disable colors in console output.
    -V, --version Show version.
    -h, --help    Print this help.
<?php
}

// Help
if (!isset($_SERVER['argv'][1]) || in_array('-h', $_SERVER['argv']) || in_array('--help', $_SERVER['argv'])) { ?>
PHP Var Dump check version <?= VERSION ?>
---------------------------
Usage:
    var-dump-check [files or directories]
<?php
    showOptions();
    exit;
}

// Version
if (in_array('-V', $_SERVER['argv']) || in_array('--version', $_SERVER['argv'])) {
    echo VERSION . PHP_EOL;
    exit;
}

$files = array(
    __DIR__ . '/../../autoload.php',
    __DIR__ . '/vendor/autoload.php'
);

$autoloadFileFound = false;
foreach ($files as $file) {
    if (file_exists($file)) {
        require $file;
        $autoloadFileFound = true;
        break;
    }
}

if (!$autoloadFileFound) {
    fwrite(STDERR,
      'You need to set up the project dependencies using the following commands:' . PHP_EOL .
      'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
      'php composer.phar install' . PHP_EOL
    );
    die(FAILED);
}

try {
    $settings = PhpVarDumpCheck\Settings::parseArguments($_SERVER['argv']);
} catch (PhpVarDumpCheck\Exception\InvalidArgument $e) {
    fwrite(STDERR, "Invalid option {$e->getArgument()}" . PHP_EOL);
    echo PHP_EOL;
    showOptions();
    die(FAILED);
}

try {
    $check = new PhpVarDumpCheck\Manager();
    $status = $check->check($settings);
    die($status ? SUCCESS : WITH_ERRORS);
} catch (PhpVarDumpCheck\Exception\Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    die(FAILED);
}