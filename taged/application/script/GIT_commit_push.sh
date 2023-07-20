#!/bin/bash

Folder=${1:-/opt/taged/taged}

User=`whoami`
History=/home/$User/crontab/history
GenDoc=/opt/taged/taged/generateDoc.sh
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
ErrFile=$History/GIT.error
RunTime=`date +"%F %T"` 
Command=$0
Err=false

echo "Run at $RunTime of $Command on $Folder" >> $LogFile

if [ -d $Folder ]
then
    
    cd $Folder

    git pull &>> $LogFile
    if [ $? -ne 0 ] 
    then 
        Err=true 
    fi

    $GenDoc

    git add -A &>>$LogFile
    if [ $? -ne 0 ] 
    then 
        Err=true 
    fi
    git commit -m "autosave"   &>> $LogFile
    if [ $? -ne 0 ] 
    then 
        Err=true 
    fi
    git push &>> $LogFile
    if [ $? -ne 0 ] 
    then 
        Err=true 
    fi

    cd - &> /dev/null
fi

if $Err
then
    touch $ErrFile
fi
