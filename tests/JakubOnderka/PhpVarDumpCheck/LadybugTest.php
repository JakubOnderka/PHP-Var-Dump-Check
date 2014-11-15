<?php

use JakubOnderka\PhpVarDumpCheck;

class LadybugTest extends PHPUnit_Framework_TestCase
{
    private $uut;


    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $settings->functionsToCheck = array_merge($settings->functionsToCheck, array(
            PhpVarDumpCheck\Settings::LADYBUG_DUMP,
            PhpVarDumpCheck\Settings::LADYBUG_DUMP_DIE,
            PhpVarDumpCheck\Settings::LADYBUG_DUMP_SHORTCUT,
            PhpVarDumpCheck\Settings::LADYBUG_DUMP_DIE_SHORTCUT,
        ));
        $this->uut = new PhpVarDumpCheck\Checker($settings);
    }


    public function testCheck_ladybugDump()
    {
        $content = <<<PHP
<?php
ladybug_dump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_ladybugDumpDie()
    {
        $content = <<<PHP
<?php
ladybug_dump_die(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_ladybugDumpShortcut()
    {
        $content = <<<PHP
<?php
ld(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_ladybugDumpDieShortcut()
    {
        $content = <<<PHP
<?php
ldd(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_dumpsWithNamespace()
    {
        $content = <<<PHP
<?php
\\ladybug_dump('Ahoj');
\\ladybug_dump_die('Ahoj');
\\ld('Ahoj');
\\ldd('Ahoj');
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(4, $result);
    }
}
