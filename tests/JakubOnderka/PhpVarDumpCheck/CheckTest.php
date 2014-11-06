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
}
