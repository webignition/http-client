<?php
namespace webignition\Http\Client;
ini_set('display_errors', 'On');

function autoload( $rootDir ) {
    spl_autoload_register(function( $className ) use ( $rootDir ) {        
        $file = sprintf(
            '%s/%s.php',
            $rootDir,
            str_replace( '\\', '/', $className )
        );        
        
        if ( file_exists($file) ) {
            require $file;
        }
    });
}

autoload( __DIR__ . '/../src');