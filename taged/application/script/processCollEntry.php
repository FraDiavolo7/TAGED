#!/usr/bin/php
<?php

$File=$argv[1];

include '../src/define.php';

$TextToParse = file_get_contents ( $File );

$Parser = new CollParser ( $TextToParse );
        
$Parser->parse ();


