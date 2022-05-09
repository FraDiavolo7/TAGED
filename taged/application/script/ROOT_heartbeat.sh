#!/bin/bash

History=$HOME/crontab/history
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
RunTime=`date +"%F %T"` 
Text="[Leviathor] I\'m alive $RunTime"
Command="/usr/bin/curl -v -G --data-urlencode phone=+33626071749 --data-urlencode apikey=338776 --data-urlencode text=\"${Text//\//}\" -L https://api.callmebot.com/signal/send.php "

echo "Run at $RunTime of $Command" >> $LogFile

#$Command &>> $LogFile
#$Command
/usr/bin/curl -G --data-urlencode phone=+33626071749 --data-urlencode apikey=338776 --data-urlencode "text=${Text//\/-/}" -L https://api.callmebot.com/signal/send.php  &>> $LogFile
/usr/bin/curl -G --data-urlencode phone=+33666171889 --data-urlencode apikey=715146 --data-urlencode "text=${Text//\/-/}" -L https://api.callmebot.com/signal/send.php  &>> $LogFile

