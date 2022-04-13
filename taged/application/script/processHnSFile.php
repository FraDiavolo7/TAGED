#!/usr/bin/php
# This script is meant to process a Hero URL file and DL the 
<?php

$File=$argv[1];

include '../src/define.php';

$FilenameItems = explode ( '_', basename ( $File ) );

$FileToDL = file_get_contents ( $File );

$TextToParse = file_get_contents ( $FileToDL );

$Parser = new HnSHeroParser ( $TextToParse, $FilenameItems );
//$Parser->parse ();


