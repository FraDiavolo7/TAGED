#!/bin/php
<?php

include '../src/define.php';

$URLpattern= 'https://%s.diablo3.blizzard.com/fr-fr/rankings/era/15/rift-%s#page=%d';

$Servers = array ( 'us', 'eu', 'kr' );

$Classes = array ( "barbarian", "crusader", "dh", "monk", "necromancer", "wd", "wizard" );

$NbPages = 7;

foreach ( $Servers as $Srv )
{
    foreach ( $Classes as $Cls )
    {
        $URL = sprintf ( $URLpattern, $Srv, $Cls, 1 );

        if ( ! is_dir ( DATA_TMP_HNS ) ) mkdir ( DATA_TMP_HNS, 0777, true );

        $TextToParse = file_get_contents ( $URL );

        $Parser = new HnSParser ( $TextToParse, $Srv, $Cls );
        $Parser->parse ();
    }
}

