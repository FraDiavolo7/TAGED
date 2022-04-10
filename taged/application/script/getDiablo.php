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
        for ( $i = 1 ; $i <= $NbPages ; ++$i )
        {
            $URL = sprintf ( $URLpattern, $Srv, $Cls, $i );
            $FileName = sprintf ( "%s_%s_%d", $Srv, $Cls, $i );
            $FilePath = DATA_TMP_HNS . $FileName;
            if ( ! is_dir ( DATA_TMP_HNS ) ) mkdir ( DATA_TMP_HNS, 0777, true );

            file_put_contents ( $FilePath, $URL );
        }
    }
}

