#!/bin/bash

Folder=$1
Separator=" : "

echo "Nb Files$Separator" `find $Folder -type f | wc -l`

echo "Size on Disk$Separator" `du -sh $Folder | awk -F ' ' ' { print $ 1 } '`
