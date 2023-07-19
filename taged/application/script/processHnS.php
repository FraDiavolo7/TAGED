#!/usr/bin/php
<?php
/**
 * @package TAGED\Scripts
 */
# This script is meant to process all Files from data storage

$ProcessFolderScr = './processHnSFolder.php';

$Children = array ();
$PoolSize = 2;

chdir ( realpath ( dirname ( __FILE__ ) ) ); // change to script dir to enable relative path used for web

include '../src/define.php';

$Data = scandir ( DATA_TMP_HNS_ADDR );

foreach ( $Data as $Folder )
{
    if ( '.' != $Folder[0] )
    {
        while ( count ( $Children ) >= $PoolSize )
        {
            foreach ( $Children as $Key => $PID ) 
            {
                $Result = pcntl_waitpid ( $PID, $Status, WNOHANG );
                
                // If the process has already exited
                if ( -1 == $Result || $Result > 0 )
                {
                    unset ( $Children [ $Key ] );
                }
            }
            
            sleep ( 60 );
        }
        
        $PID = pcntl_fork ();
        
        if ( -1 == $PID )
        {
            // Echec
        }
        
        else if ( $PID )
        {
            // Parent
            $Children [] = $PID;
        }
        
        else
        {
            $Command = "$ProcessFolderScr $Folder";
            
            system ( $Command );
            
            exit();
        }
    }
}


