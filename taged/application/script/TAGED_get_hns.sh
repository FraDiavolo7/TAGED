#!/bin/bash


User=`whoami`
History=/home/$User/crontab/history
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
RunTime=`date +"%F %T"` 
Command=/opt/taged/taged/taged/application/script/getDiablo.php

echo "Run at $RunTime of $Command" >> $LogFile

$Command &>> $LogFile
