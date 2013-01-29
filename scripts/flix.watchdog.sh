#!/bin/bash
. /etc/kaltura.d/system.ini

if [ `ps -ef | grep -c [f]lixd` -lt 1 ]
   then
      sleep 30
      if [ `ps -ef | grep -c [f]lixd` -lt 1 ]
         then
            echo "flixd on `hostname` was restarted" | mail -s "flixd service not found on `hostname`" $MAILTO
             /etc/init.d/flixengine start
         fi
fi
