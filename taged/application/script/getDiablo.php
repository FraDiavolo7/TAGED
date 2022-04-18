#!/bin/php
<?php
# This script retrieves all Hero URL from le Diablo 3 Ladder for each servers and each class

include '../src/define.php';

$URLpattern= 'https://%s.diablo3.blizzard.com/fr-fr/rankings/era/15/rift-%s#page=%d';

$Servers = array ( 'us', 'eu', 'kr' );

$Classes = array ( "barbarian", "crusader", "dh", "monk", "necromancer", "wd", "wizard" );

foreach ( $Servers as $Srv )
{
    foreach ( $Classes as $Cls )
    {
        $URL = sprintf ( $URLpattern, $Srv, $Cls, 1 );

        if ( ! is_dir ( DATA_TMP_HNS ) ) mkdir ( DATA_TMP_HNS, 0777, true );

        $TextToParse = file_get_contents ( $URL );
        file_put_contents ( DATA_TMP_HNS . '/toto1', $TextToParse);

        $Parser = new HnSParser ( $TextToParse, $URL, $Srv, $Cls );
        $Parser->parse ();
        exit;
    }
}

