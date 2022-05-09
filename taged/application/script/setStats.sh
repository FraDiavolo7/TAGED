#!/bin/bash

SCRIPT=/opt/taged/taged/taged/application/script/getStats.sh

function set_stats ()
{
    Espace=$1

    Folder=/home/taged/data/

    $SCRIPT $Folder $Espace > $Folder/stat_$Espace
}

set_stats collection
set_stats hackNslash
set_stats match3
