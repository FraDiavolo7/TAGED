#!/bin/bash

DataFolder=$1
Space=$2
Separator=" : "

function getFolderStat ()
{
    local InFolder=$1
    local Label=$2

    if [ -d $InFolder ]
    then
        echo "$Label Files$Separator" `find $InFolder -type f ! -name ".*" | wc -l`
        echo "$Label Size$Separator" `du -sh $InFolder | awk -F ' ' ' { print $ 1 } '`
    fi
}

function getFolderData ()
{
    local Folder=$DataFolder/$1
    local ErrorFolder=$DataFolder/errors/$1
    local ArchiveFolder=$DataFolder/archive/$1
    local LabelExt=''

    if [ "" != "$2" ]
    then
        LabelExt="_$2"
    fi

    getFolderStat $Folder "Data$LabelExt"
    getFolderStat $ErrorFolder "Errors$LabelExt"
    getFolderStat $ArchiveFolder "Archive$LabelExt"
}

function handleOptionnal ()
{
    Opt=$1
    Optionnal=$Space/$Opt
    OptFolder=$DataFolder/$Optionnal

    if [ -d $OptFolder ]
    then
         getFolderData $Optionnal $Opt

    fi
}

getFolderData $Space

handleOptionnal gen1ou 
handleOptionnal gen2ou 
handleOptionnal gen3ou 
handleOptionnal gen4ou 
