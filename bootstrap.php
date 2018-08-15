<?php
/**
 * Enable autoloading of plugin classes in namespace
 * @param $class_name
 */
function autoload( $className ) {
    require_once __DIR__ . '/vendor/' . str_replace("\\", "/", $className) . '.php';
}

spl_autoload_register('\autoload' );