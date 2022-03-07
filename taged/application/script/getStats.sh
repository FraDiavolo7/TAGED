#!/bin/bash

Folder=$1
Separator=" : "

function handleOptionnal ()
{
    Optionnal=$1
    OptFolder=$Folder/$Optionnal

    if [ -d $OptFolder ]
    then

         echo "Nb Files $Optionnal$Separator" `find $OptFolder -type f | wc -l`

    fi
}


echo "Nb Files$Separator" `find $Folder -type f | wc -l`

echo "Size on Disk$Separator" `du -sh $Folder | awk -F ' ' ' { print $ 1 } '`

handleOptionnal gen1ou
