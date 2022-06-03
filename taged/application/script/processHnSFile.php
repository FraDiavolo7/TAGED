#!/usr/bin/php
<?php
# This script is meant to process a Hero URL file and DL the 

$File=realpath ( $argv[1] ); // absolute path
chdir ( realpath ( dirname ( __FILE__ ) ) ); // change to script dir to enable relative path used for web

function moveFile ( $File, $Dest )
{
    $DestDir = dirname ( $Dest );
    if ( ! file_exists ( $DestDir ) ) mkdir ( $DestDir );
    rename ( $File, $Dest );
}

if ( file_exists ( $File ) )
{
    include '../src/define.php';

    $FilenameItems = explode ( '_', basename ( $File ) );
    $FilenameItems [] = $File;

    $ErrorPath   = str_replace ( DATA_TMP_HNS_ADDR, DATA_ERRORS_HNS,  $File );
    $ArchivePath = str_replace ( DATA_TMP_HNS_ADDR, DATA_ARCHIVE_HNS, $File );
    
    $FileToDL = file_get_contents ( $File );

    $TextToParse = @file_get_contents ( $FileToDL );
        
    if ( FALSE === $TextToParse )
    {
        $URLPlayerField = 5;
        $URLItems = explode ( '/', $FileToDL );
        $URLItems [$URLPlayerField] = urlencode ( $URLItems [$URLPlayerField] );
        $TextToParse = file_get_contents ( implode ( '/', $URLItems ) );
    }

    $CopyPath = str_replace ( '/addr/', '/files/', $File );
    $CopyDir = dirname ( $CopyPath );
    if ( ! file_exists ( $CopyDir ) )
        mkdir ( $CopyDir );
    file_put_contents ( $CopyPath, $TextToParse );

    $Parser = new HnSHeroParser ( $TextToParse, $FilenameItems );
    try 
    {
        $Parser->parse ();
        moveFile ( $File, $ArchivePath );
    }
    
    catch ( Throwable $Exc ) 
    {
        Log::error ( 'Throwable reçu : ' .  $Exc->getMessage() );
        moveFile ( $File, $ErrorPath );
    }
    
    catch ( Exception $Exc )
    {
        Log::error ( 'Exception reçue : ' .  $Exc->getMessage() );
        moveFile ( $File, $ErrorPath );
    }
   
}
