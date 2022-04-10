#!/bin/bash

DataManager=/home/taged/data/datamanager.sh
AppliType=collection
Process=`dirname $0`/processCollEntry.php

Entries=("gen1ou")

for Entry in ${Entries[@]}
do
    for File in `$DataManager GETALL $AppliType $Entry`
    do
        $Process $File
    done
done


