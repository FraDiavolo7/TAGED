#!/bin/bash

History=$HOME/crontab/history
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
RunTime=`date +"%F %T"` 
LastLog=`ls -t $History/reboot* | head -1`
Text=`cat $LastLog`
Command="/usr/bin/curl -v -G --data-urlencode phone=+33626071749 --data-urlencode apikey=338776 --data-urlencode text=\"${Text//\//}\" -L https://api.callmebot.com/signal/send.php "

echo "Run at $RunTime of $Command" >> $LogFile

/opt/taged/taged/taged/application/script/SIGNAL_send.sh $Text
