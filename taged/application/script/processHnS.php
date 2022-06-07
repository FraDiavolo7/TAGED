#!/usr/bin/php
<?php
# This script is meant to process all Files from data storage

$ProcessFolderScr = './processHnSFolder.php';

chdir ( realpath ( dirname ( __FILE__ ) ) ); // change to script dir to enable relative path used for web

include '../src/define.php';

$Data = scandir ( DATA_TMP_HNS_ADDR );

foreach ( $Data as $Folder )
{
    if ( '.' != $Folder[0] )
    {
        $Command = "$ProcessFolderScr $Folder";

        system ( $Command );
    }
}


