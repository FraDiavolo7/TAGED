#!/bin/bash

DIR=`dirname $0`
cd $DIR &> /dev/null

ExecFile=`basename $0`.run

if [ -f $ExecFile ]
then
    echo "Already running"
    exit
fi

touch $ExecFile

DataManager=/home/taged/data/datamanager.sh
AppliType=collection
Process=`dirname $0`/processCollEntry.php

Entries=("gen1ou" "gen2ou" "gen3ou" "gen4ou")

for Entry in ${Entries[@]}
do
    for File in `$DataManager GETALL $AppliType $Entry`
    do
        FileEntry=`basename $File`
        Result=`$Process $File`
    
        if [ "$Result" == "OK" ]
        then
            $DataManager REMOVE $AppliType $FileEntry
        else
            $DataManager ERROR $AppliType $FileEntry
            echo $Result;
        fi
    done
done

rm $ExecFile

cd - &> /dev/null
