<?php
/**
 *
 * @package TAGED\Maintenance
 */

set_include_path ( get_include_path() . ":.:../src:../src/modules:../src/pages:../../../commun/src");

spl_autoload_register(function ($class_name) {
    include $class_name . '.class.php';
});

