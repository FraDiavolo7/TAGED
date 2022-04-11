#!/usr/bin/php
<?php

$File=$argv[1];

include '../src/define.php';

$FilenameItems = explode ( '_', basename ( $File ) );

$FileToDL = file_get_contents ( $File );

$TextToParse = file_get_contents ( $FileToDL );

if ( count ( $FilenameItems ) == 3 )
{
    $Parser = new HnSParser ( $TextToParse, $FilenameItems [0], $FilenameItems [1] );
    $Parser->parse ();
}
else
{
    echo "Hero File!!! " . print_r ( $FilenameItems, TRUE ) . "\n";
    //$Parser = new HnSHeroParser ( $TextToParse, $FilenameItems );
    //$Parser->parse ();
}


