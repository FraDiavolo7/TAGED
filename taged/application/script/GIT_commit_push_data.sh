#!/bin/bash

Folder=${1:-/home/taged/data}

User=`whoami`
History=/home/$User/crontab/history
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
RunTime=`date +"%F %T"` 
Command=$0

echo "Run at $RunTime of $Command" >> $LogFile

if [ -d $Folder ]
then
    
    cd $Folder

    git pull &>> $LogFile
    git add -A &>>$LogFile
    git commit -m "autosave"   &>> $LogFile
    git push &>> $LogFile

    cd - &> /dev/null
fi

