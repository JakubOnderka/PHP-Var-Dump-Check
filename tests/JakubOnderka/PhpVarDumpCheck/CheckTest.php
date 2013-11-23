<?php

use JakubOnderka\PhpVarDumpCheck;

class CheckTest extends PHPUnit_Framework_TestCase
{
    protected $uut;

    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $this->uut = new PhpVarDumpCheck\Checker($settings);
    }

    public function testCheck_emptyFile_noDump()
    {
        $content = <<<PHP
<?php

PHP;

        $result = $this->uut->check($content);

        $this->assertCount(0, $result);
    }

    public function testCheck_singleVarDump_dump()
    {
        $content = <<<PHP
<?php
var_dump('Ahoj');
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('var_dump', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singlePrintR_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj');
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singleVarExport_dump()
    {
        $content = <<<PHP
<?php
var_export('Ahoj');
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('var_export', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }


    /*
     * Function parsing
     */

    public function testCheck_templateSingleVarExport_dump()
    {
        $content = <<<PHP
var_export('Ahoj');
<?php
var_export('Ahoj');
?>
var_export('Ahoj');
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('var_export', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(3, $result[0]->getLineNumber());
    }

    public function testCheck_singleVarExportWhitespaces_dump()
    {
        $content = <<<PHP
<?php
var_export (  'Ahoj'
 ) ;
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('var_export', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singleVarExportWhitespaces_comments()
    {
        $content = <<<PHP
<?php
var_export /* v */ ( /* v */ 'Ahoj'/* v */
 ) ;
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('var_export', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    /*
     * Second parameters test
     */

    public function testCheck_singlePrintRWithReturnFalse_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', false);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singlePrintRWithReturnFalseComments_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj'/**/,/**/false/**/);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singlePrintRWithReturnNull_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', null);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singlePrintRWithReturnIntZero_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', 0);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singlePrintRWithReturnFloatZero_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', 0.0);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
    }

    public function testCheck_singlePrintRWithReturnFalseVariableAssign_dump()
    {
        $content = <<<PHP
<?php
print_r('Ahoj', \$var = false);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);

        $this->assertEquals('print_r', $result[0]->getType());
        $this->assertTrue($result[0]->isSure());
        $this->assertEquals(2, $result[0]->getLineNumber());
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

    public function testCheck_zendDebugDump()
    {
        $content = <<<PHP
<?php
Zend_Debug::dump(\$var);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);
    }

    public function testCheck_zendDebugDumpReturn()
    {
        $content = <<<PHP
<?php
Zend_Debug::dump(\$var, null, false);
PHP;

        $result = $this->uut->check($content);

        $this->assertCount(1, $result);
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

    /**
     * Namespaces
     */

    public function testCheck_zendNamespaceDump()
    {
        $content = <<<PHP
<?php
\Zend\Debug\Debug::dump(\$form);
PHP;

        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }
}