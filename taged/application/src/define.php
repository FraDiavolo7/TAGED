<?php
set_include_path ( get_include_path() . ";.;../src;../src/modules;../src/pages;../src/parse;../../../commun/src");

spl_autoload_register(function ($class_name) {
    include $class_name . '.class.php';
});

