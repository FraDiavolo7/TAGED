#!/bin/bash

History=$HOME/crontab/history
LogFile=$History/`basename $0`_`date +%Y%m%d`.log
RunTime=`date +"%F %T"` 
ErrFile=$History/GIT.error
LastLog=`ls -t $History/GIT_commit_push.sh* | head -1`
if [ -f $ErrFile ]
then

    Text=`cat $LastLog | tr -d '"'`
    Command="/usr/bin/curl -v -G --data-urlencode phone=+33626071749 --data-urlencode apikey=338776 --data-urlencode text=\"${Text//\//}\" -L https://api.callmebot.com/signal/send.php "

    echo "Run at $RunTime of $Command" &>> $LogFile

    /opt/taged/taged/taged/application/script/SIGNAL_send.sh $Text

fi
