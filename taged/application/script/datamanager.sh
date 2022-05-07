#!/bin/bash

function usage ()
{
    echo "$0 <VERB> <APP> {<ITEM> ...}"
    echo "   STORE <APP> <FILE>"
    echo "   GETNEXT <APP>"
    echo "   REMOVE <APP> <ENTRY>"
    echo "   EXISTS <APP> <ENTRY>"
    echo "   GET <APP> <ENTRY>"

}

DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

Action=$1
Appli=$2

DataFileName=.datafile

shift 2

function getCollectionEntryFolder ()
{
    Entry=$1

    BaseFolder=${Entry%%-*}
    EndName=${Entry##*-}
    SubRep=${EndName:0:5}

    Tirets=${Entry//[^-]}
    if [ ${#Tirets} -eq 2 ]
    then
        SubRep=${Entry%%-*}
        Tmp=${Entry#*-}
        BaseFolder=${Tmp%%-*}
    elif [ ${#Tirets} -eq 0 ]
    then
        SubRep=""
    fi

    InnerFolder=$BaseFolder/$SubRep

    echo $InnerFolder
}

function getEntryFolder ()
{
    Entry=$1
    InnerFolder=$Entry

    case "$Appli" in
        "collection") InnerFolder=`getCollectionEntryFolder $Entry` ;;
        *) InnerFolder=${Entry:0:2} ;;
    esac

    echo $InnerFolder
}


function storeFile ()
{
    FileToStore=$1
    
    if [ "" != "$FileToStore" ]
    then
        FileName=`basename $FileToStore`
        InnerFolder=`getEntryFolder $FileName`

        DestFolder=$DIR/$Appli/$InnerFolder
        DataFile=$DIR/$Appli/$InnerFolder/$DataFileName

        if [ -f $FileToStore ]
        then
            if [ ! -d $DestFolder ]
            then
                mkdir -p $DestFolder
            fi
            
            if ! [ -f $DataFile ] || ! grep -q $FileName $DataFile
            then
                echo "Storing $FileToStore into $DestFolder"
                cp $FileToStore $DestFolder
                echo $FileName >> $DataFile
            fi
        fi
    else
        usage
    fi
}

function existsEntry ()
{
    Entry=$1

    echo "Testing Entry  existence $Entry"


    InnerFolder=`getEntryFolder $Entry`

    DataFile=$DIR/$Appli/$InnerFolder/$DataFileName
    
    if [ -e $DataFile ] && grep -q $Entry $DataFile
    then
        echo "$Entry exists"
        exit 0
    else
        echo "$Entry does not exists"
        exit 1
    fi

}

function removeEntry ()
{
    Entry=$1

    InnerFolder=`getEntryFolder $Entry`

    DataFile=$DIR/$Appli/$InnerFolder/$Entry
    ArchiveFolder=$DIR/archive/$Appli/$InnerFolder
    ArchiveFile=$ArchiveFolder/$Entry
    
    if [ -e $DataFile ]
    then
        echo "Removing Entry $Entry from $Appli"
        mkdir -p $ArchiveFolder &> /dev/null
        mv $DataFile $ArchiveFile
    fi

}

function errorEntry ()
{
    Entry=$1

    InnerFolder=`getEntryFolder $Entry`

    DataFile=$DIR/$Appli/$InnerFolder/$Entry
    ErrorFolder=$DIR/errors/$Appli/$InnerFolder
    ErrorFile=$ErrorFolder/$Entry
    
    if [ -e $DataFile ]
    then
        echo "Moving Entry $Entry from $Appli to Error"
        mkdir -p $ErrorFolder &> /dev/null
        mv $DataFile $ErrorFile
    fi

}
function getEntries ()
{
    Entry=$1
    if [ "" != "$Entry" ]
    then
        InnerFolder=`getEntryFolder $Entry`
        DataFolder=$DIR/$Appli/$InnerFolder
        find $DataFolder -type f -name "[^.]*"
    fi
}

function getEntry ()
{
    Entry=$1

    echo "Getting Entry $Entry"
}

if [ "" = "$Appli" ] 
then
    usage
else

    case "$Action" in
        "STORE")  storeFile   $@ ;;
        "GET")    getEntry    $@ ;;
        "GETALL") getEntries  $@ ;;
        "REMOVE") removeEntry $@ ;;
        "ERROR")  errorEntry  $@ ;;
        "EXISTS") existsEntry $@ ;;
        *) usage ;;
    esac
fi

