<?php

use JakubOnderka\PhpVarDumpCheck;

class ZendTest extends PHPUnit_Framework_TestCase
{
    protected $uut;


    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $settings->functionsToCheck = array_merge($settings->functionsToCheck, array(
            PhpVarDumpCheck\Settings::ZEND_DEBUG_DUMP,
            PhpVarDumpCheck\Settings::ZEND_DEBUG_DUMP_2,
        ));

        $this->uut = new PhpVarDumpCheck\Checker($settings);
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


    /**
     * Namespaces
     */
    public function testCheck_zendNamespaceDump()
    {
        $content = <<<PHP
<?php
\\Zend\\Debug\\Debug::dump(\$form);
PHP;

        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }
}
