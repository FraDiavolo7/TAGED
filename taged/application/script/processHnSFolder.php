#!/usr/bin/php
<?php
# This script is meant to process all Files from data storage

$ProcessFileScr = './processHnSFile.php';

chdir ( realpath ( dirname ( __FILE__ ) ) ); // change to script dir to enable relative path used for web

include '../src/define.php';

$Folder=DATA_TMP_HNS_ADDR . '/' . $argv [1];
if ( is_dir ( $Folder ) )
{
    $Data = scandir ( $Folder );

    foreach ( $Data as $File )
    {
        if ( '.' != $File [0] )
        {
            $FilePath = $Folder . '/' . $File;

            if ( file_exists ( $FilePath ) )
            {
                $Command = "$ProcessFileScr $FilePath";

                system ( $Command );

//                sleep ( 1 );
//                usleep ( 1000 );
            }
            
        }
    }

    rmdir ( $Folder );
}

