<?php
/*
 * This file bootstraps the test environment.
 */

error_reporting(E_ALL | E_STRICT);

// register silently failing autoloader
/*
spl_autoload_register(function($class)
{

    echo $class."\n";
    //if (0 === strpos($class, 'Doctrine\Tests\\')) {
        $path = __DIR__.'/../src/'.strtr($class, '\\', '/').'.php';
        echo $path."\n";
        if (is_file($path) && is_readable($path)) {
            require_once $path;
            return true;
        }
    //}
});
*/

require_once __DIR__ . "/../vendor/autoload.php";