#!/bin/bash

URL=https://replay.pokemonshowdown.com/

DataManager=/opt/taged/data/datamanager.sh
AppliType=collection

TmpFolder=/tmp/taged
WGetResult=$TmpFolder/WGetResult
WGetReplayResult=$TmpFolder/WGetReplayResult

function handleReplay () 
{
    ReplayLink=$1
    Entry=`basename $ReplayLink`

    $DataManager EXISTS $AppliType $Entry &> /dev/null
    if [ $? -ne 0 ]
    then
        echo -n "Handling $ReplayLink "
        echo Downloading $Entry
        wget --no-cache $URL$ReplayLink -P $TmpFolder -nv &> $WGetReplayResult

        DLReplayFile=`cat $WGetReplayResult | grep "$URL" | awk -F '"' ' { print $2 } '`

        $DataManager STORE $AppliType $DLReplayFile

        rm $DLReplayFile
    fi
}



if [ ! -d $TmpFolder ]
then
    mkdir -p $TmpFolder &> /dev/null
fi

if [ ! -d $TmpFolder ]
then
    echo "$TmpFolder cannot be created, stopping."
    exit 1
fi

wget --no-cache $URL -P $TmpFolder -nv &> $WGetResult

DLFile=`cat $WGetResult | grep "$URL" | awk -F '"' ' { print $2 } '`

if [ -f $DLFile ]
then
    echo "Downloaded $DLFile from $URL"

    for File in `cat $DLFile | grep "<li>" | grep "<a " | awk -F '"' ' { print $2 } ' | grep ^/`
    do
         handleReplay $File
    done    

    rm $DLFile
else
    echo "Download failed."
    cat $WGetResult
fi


