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
            PhpVarDumpCheck\Settings::DEBUGGER_DUMP_SHORTCUT,
			PhpVarDumpCheck\Settings::DEBUGGER_BARDUMP,
            PhpVarDumpCheck\Settings::DEBUGGER_BARDUMP_SHORTCUT,
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


    public function testCheck_tracyDebugBarDump()
    {
        $content = <<<PHP
<?php
Debugger::barDump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_tracyDebugShortcutDump()
    {
        $content = <<<PHP
<?php
dump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_tracyDebugShortcutBarDump()
    {
        $content = <<<PHP
<?php
bdump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_dumpsWithNamespace()
    {
        $content = <<<PHP
<?php
\\dump(\$var);
\\bdump(\$var);
\\Debugger::dump(\$var);
\\Debugger::barDump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(4, $result);
    }
}
