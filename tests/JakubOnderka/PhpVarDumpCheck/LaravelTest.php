<?php

use JakubOnderka\PhpVarDumpCheck;

class LaravelTest extends PHPUnit_Framework_TestCase
{
    private $uut;


    public function __construct()
    {
        $settings = new PhpVarDumpCheck\Settings();
        $settings->functionsToCheck = array_merge($settings->functionsToCheck, array(
            PhpVarDumpCheck\Settings::LARAVEL_DUMP_DD,
        ));
        $this->uut = new PhpVarDumpCheck\Checker($settings);
    }


    public function testCheck_laravelDumpDd()
    {
        $content = <<<PHP
<?php
dd(\$var);
PHP;
        $result = $this->uut->check($content);
        $this->assertCount(1, $result);
    }
}
