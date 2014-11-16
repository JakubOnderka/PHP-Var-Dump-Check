<?php

use JakubOnderka\PhpVarDumpCheck;

class TracyTest extends PHPUnit_Framework_TestCase
{
    private $uut;


    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $settings->functionsToCheck = array_merge($settings->functionsToCheck, array(
            PhpVarDumpCheck\Settings::DEBUGGER_DUMP,
        ));
        $this->uut = new PhpVarDumpCheck\Checker($settings);
    }


    public function testCheck_tracyDebugDump()
    {
        $content = <<<PHP
<?php
Debugger::dump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_dumpWithNamespace()
    {
        $content = <<<PHP
<?php
\\Debugger::dump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }
}
