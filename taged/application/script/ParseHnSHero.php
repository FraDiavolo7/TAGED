#!/usr/bin/php
# This script is meant to process a Hero URL file and DL the 
<?php

$File=$argv[1];

include '../src/define.php';

$FilenameItems = array ( 'a', 'b', 'c' );

$TextToParse = file_get_contents ( $File );

$Parser = new HnSHeroParser ( $TextToParse, $FilenameItems );
$Parser->parse ();


