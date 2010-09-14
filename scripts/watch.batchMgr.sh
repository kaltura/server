#!/bin/bash
MAILTO="alex.bandel@kaltura.com"
KP=$(pgrep -P 1 -f KGenericBatchMgr.class.php)
MAINT=/opt/kaltura/maintenance
if [ "X$KP" = "X" ]
   then
      sleep 10
      KP=$(pgrep -P 1 -f KGenericBatchMgr.class.php)
      if [[ "X$KP" = "X" && ! -f $MAINT ]]
         then
            echo "KGenericBatchMgr.class.php `hostname` was restarted" | mail -s "KGenericBatchMgr.class.php service not found on `hostname`" $MAILTO
            /sbin/service batchMgr restart
         fi
fi
