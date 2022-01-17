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

shift 2

function getEntryFolder ()
{
    Entry=$1
    InnerFolder=$Entry

    case "$Appli" in
        "collection") InnerFolder=${Entry%%-*} ;;
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

        if [ -f $FileToStore ]
        then
            if [ ! -d $DestFolder ]
            then
                mkdir -p $DestFolder
            fi
            echo "Storing $FileToStore into $DestFolder"
            cp $FileToStore $DestFolder
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

    DataFile=$DIR/$Appli/$InnerFolder/$Entry
    echo $DataFile
    
    if [ -e $DataFile ]
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

    echo "Removing Entry $Entry"
}

function getNextEntry ()
{
    echo "Getting next Entry for $Appli"
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
        "STORE")   storeFile    $@ ;;
        "GET")     getEntry     $@ ;;
        "GETNEXT") getNextEntry $@ ;;
        "REMOVE")  removeEntry  $@ ;;
        "EXISTS")  existsEntry  $@ ;;
        *) usage ;;
    esac
fi

