PHP VarDump Check
=================

PHP console application for find forgotten variable dump. Support PHP build in method
`print_r`, `var_dump` and `var_export` method and also method from Tracy debugger, Ladybug,
Symfony, Laravel, Doctrine and Zend Framework.

Install
-------

Just create a `composer.json` file and run the `php composer.phar install` command to install it:

```json
{
    "require-dev": {
        "jakub-onderka/php-var-dump-check": "~0.2"
    }
}
```

For colored output install suggested package `jakub-onderka/php-console-highlighter`.

Usage and example output
--------------

```
$ ./vendor/bin/var-dump-check --no-colors --tracy .
...................X...

Checked 23 files in 0.1 second, dump found in 1 file

------------------------------------------------------------
Forgotten dump 'var_dump' found in ./test.php:36
    34|         $functionsToCheck = $this->prepareFunctionCheck($this->settings->functionsToCheck);
    35|
  > 36| 	    var_dump($functionsToCheck);
    37|
    38|         foreach ($tokens as $key => $token) {
```

Options for run
---------------

- none - check dump: `var_dump`, `var_export`, `print_r`
- `--ladybug` - check dump: `ladybug_dump`, `ladybug_dump_die`, `ld`, `ldd`
- `--tracy` - check dump: `dump`, `bdump`, `Debugger::dump`, `Debugger::barDump`
- `--zend` - check dump: `Zend_Debug::dump`, `\Zend\Debug\Debug::dump`
- `--doctrine` - check dump: `Doctrine::dump`, `\Doctrine\Common\Util\Debug::dump`
- `--symfony` - check dump: `dump`, `VarDumper::dump`, `VarDumper::setHandler`
- `--laravel` - check dump: `dd`
- `--no-colors` - disable colors from output
- `--exclude folder/` - exclude *folder/* from check
- `--extensions php,phpt,php7` - map file extensions for check

Recommended setting for usage with Symfony framework
--------------

For run from command line:

```
$ ./vendor/bin/var-dump-check --symfony --exclude app --exclude vendor .
```

or setting for ANT:

```xml
<condition property="var-dump-check" value="${basedir}/bin/var-dump-check.bat" else="${basedir}/bin/var-dump-check">
    <os family="windows"/>
</condition>

<target name="var-dump-check" description="Run PHP VarDump check">
    <exec executable="${var-dump-check}" failonerror="true">
        <arg line='--exclude ${basedir}/app/' />
        <arg line='--exclude ${basedir}/vendor/' />
        <arg line='${basedir}' />
    </exec>
</target>
```

------

[![Build Status](https://travis-ci.org/JakubOnderka/PHP-Var-Dump-Check.svg?branch=master)](https://travis-ci.org/JakubOnderka/PHP-Var-Dump-Check)
[![Downloads this Month](https://img.shields.io/packagist/dm/jakub-onderka/php-var-dump-check.svg)](https://packagist.org/packages/jakub-onderka/php-var-dump-check)
[![Latest stable](https://img.shields.io/packagist/v/jakub-onderka/php-var-dump-check.svg)](https://packagist.org/packages/jakub-onderka/php-var-dump-check)
