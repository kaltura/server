#!/bin/bash
#if [ -L $0 ];then
#	REAL_SCRIPT=`readlink $0`
#else
#	REAL_SCRIPT=$0
#fi
#. `dirname $REAL_SCRIPT`/../configurations/system.ini

if [ `ps -ef | grep -c [f]lixd` -lt 1 ]
   then
      sleep 30
      if [ `ps -ef | grep -c [f]lixd` -lt 1 ]
         then
            echo "flixd on `hostname` was restarted" | mail -s "flixd service not found on `hostname`" servicealert@kaltura.com
             /etc/init.d/flixengine start
         fi
fi
