PHP VarDump Check
=================

PHP console application for find forgotten variable dump. Support PHP build in method
print_r, var_dump and var_export method and also method from Tracy debugger, Ladybug
and Zend Framework.

Install
-------

Just create a `composer.json` file and run the `php composer.phar install` command to install it:

```json
{
    "require-dev": {
        "jakub-onderka/php-var-dump-check": "0.*"
    }
}
```

For colored output install suggested package `jakub-onderka/php-console-highlighter`.

Usage and example output
--------------

```
$ ./vendor/bin/var-dump-check .
...................X...

Checked 23 files in 0.1 second, dump found in 1 file

------------------------------------------------------------
Forgotten dump 'var_dump' found in ./test.php:36
    34|         $functionsToCheck = $this->prepareFunctionCheck($this->settings->functionsToCheck);
    35|
  > 36| 	      var_dump($functionsToCheck);
    37|
    38|         foreach ($tokens as $key => $token) {
```
