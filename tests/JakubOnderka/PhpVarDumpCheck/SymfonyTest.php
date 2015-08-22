<?php

use JakubOnderka\PhpVarDumpCheck;

class SymfonyTest extends PHPUnit_Framework_TestCase
{
    private $uut;


    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $settings->functionsToCheck = array_merge($settings->functionsToCheck, array(
            PhpVarDumpCheck\Settings::SYMFONY_VARDUMPER_HANDLER,
            PhpVarDumpCheck\Settings::SYMFONY_VARDUMPER_DUMP,
            PhpVarDumpCheck\Settings::SYMFONY_VARDUMPER_DUMP_SHORTCUT,
        ));
        $this->uut = new PhpVarDumpCheck\Checker($settings);
    }


    public function testCheck_symfonyDebugDump()
    {
        $content = <<<PHP
<?php
VarDumper::dump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }

    public function testCheck_symfonyDebugSetHandler()
    {
        $content = <<<PHP
<?php
VarDumper::setHandler(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }

    public function testCheck_symfonyDebugSetHandlerFunction()
    {
        $content = <<<PHP
<?php
VarDumper::setHandler(function(\\Exception \$e){

});
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_symfonyDebugShortcutDump()
    {
        $content = <<<PHP
<?php
dump(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }


    public function testCheck_symfonyDumpsWithNamespace()
    {
        $content = <<<PHP
<?php
\\dump(\$var);
\\Symfony\\Component\\VarDumper\\VarDumper::dump(\$var);
\\Symfony\\Component\\VarDumper\\VarDumper::setHandler(\$var);
\\Symfony\\Component\\VarDumper\\VarDumper::setHandler(function(){

});
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(4, $result);
    }
}
