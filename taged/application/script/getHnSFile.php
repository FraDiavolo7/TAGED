#!/usr/bin/php
<?php
/**
 * @package TAGED\Scripts
 */
# This script takes the first folder from HNS TMP ADDR and fetches all files contents

include '../src/define.php';

$MainFolder = DATA_TMP_HNS_ADDR;
$DestFolder = DATA_TMP_HNS_FILES;

if ( is_dir ( $MainFolder ) )
{
    $Folders = scandir ( $MainFolder );
    
    foreach ( $Folders as $Folder )
    {
        $FolderPath = $MainFolder . '/' . $Folder;
        $DestFolderPath = $DestFolder . '/' . $Folder;
        
        if ( ( '.' != $Folder[0] ) &&  ( is_dir ( $FolderPath ) ) )
        {
            // In a Addr folder
            echo "scanning $FolderPath\n";
            $Files = scandir ( $FolderPath );
            
            if ( ! is_dir ( $DestFolderPath ) ) mkdir ( $DestFolderPath, 0777, true );
            
            foreach ( $Files as $File )
            {
                $FilePath = $FolderPath . '/' . $File;
                $DestFilePath = $DestFolderPath . '/' . $File;
                echo "handling $FilePath\n";
                
                if ( ( '.' != $File[0] ) &&  ( is_file ( $FilePath ) ) )
                {
                    $FileToDL = file_get_contents ( $FilePath );
                    
                    $TextToParse = file_get_contents ( $FileToDL );
                    
                    file_put_contents ( $DestFilePath, $TextToParse );
                }
            }
            echo "rm $FolderPath (non fait)\n";
            sleep (10);
            exit;
        }
    }
}

