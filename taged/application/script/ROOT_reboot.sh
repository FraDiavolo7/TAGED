#!/bin/bash

History=$HOME/crontab/history
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
RunTime=`date +"%F %T"` 
Command=/usr/sbin/reboot

echo "Run at $RunTime of $Command" >> $LogFile

$Command &>> $LogFile

