#!/usr/bin/php
<?php
/**
 * @package TAGED\Scripts
 */

$File=$argv[1];

include '../src/define.php';

$Result = 'OK';

$TextToParse = file_get_contents ( $File );

$Parser = new CollParser ( $TextToParse );
        
try 
{
    $Parser->parse ();
} 
catch (Exception $e) 
{
    $Result = 'KO - ' . $File . ' Exception : ' . $e->getMessage();
}

echo $Result . "\n"; 
