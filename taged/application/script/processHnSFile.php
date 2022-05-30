#!/usr/bin/php
<?php
# This script is meant to process a Hero URL file and DL the 

$File=realpath ( $argv[1] ); // absolute path
chdir ( realpath ( dirname ( __FILE__ ) ) ); // change to script dir to enable relative path used for web

if ( file_exists ( $File ) )
{
    include '../src/define.php';

    $FilenameItems = explode ( '_', basename ( $File ) );
    $FilenameItems [] = $File;

    $FileToDL = file_get_contents ( $File );

    $TextToParse = file_get_contents ( $FileToDL );
    if ( FALSE === $TextToParse )
        $TextToParse = file_get_contents ( urlencode ( $FileToDL ) );

    file_put_contents ( '/home/taged/data/hns_tmp/test.html', $TextToParse );

    $Parser = new HnSHeroParser ( $TextToParse, $FilenameItems );
    $Parser->parse ();

}
