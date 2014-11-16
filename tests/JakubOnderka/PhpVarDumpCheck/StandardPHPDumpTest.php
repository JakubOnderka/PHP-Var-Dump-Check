<?php

use JakubOnderka\PhpVarDumpCheck;

class StandardPHPDumpTest extends PHPUnit_Framework_TestCase
{
    protected $uut;


    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $this->uut = new PhpVarDumpCheck\Checker($settings);
    }


    public function testCheck_singlePrintRWithReturnTrue_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', true);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_singlePrintRWithCapitalizedReturnTrue_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', TRUE);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_singlePrintRWithReturnIntOne_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', 1);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_singlePrintRWithReturnFloatOne_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', 1.1);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_singlePrintRWithReturnTrueVariableAssign_dum()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', \$var = true);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_singlePrintRWithReturnTrueMultipleVariableAssign_dum()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', \$var = \$var2 =  true);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_singleVarExportWithReturnTrue_dump()
    {
        $content = <<<PHP
<?php
var_export('Ahoj', true);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(0, $result);
    }


    public function testCheck_dumpsWithNamespace()
    {
        $content = <<<PHP
<?php
\\print_r('Ahoj');
\\var_dump('Ahoj');
\\var_export('Ahoj');
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(3, $result);
    }
}
