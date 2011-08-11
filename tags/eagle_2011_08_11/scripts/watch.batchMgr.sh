#!/bin/bash
MAILTO="@ADMIN_CONSOLE_ADMIN_MAIL@"
KP=$(pgrep -P 1 -f KGenericBatchMgr.class.php)
MAINT=@BASE_DIR@/maintenance
if [ "X$KP" = "X" ]
   then
      sleep 10
      KP=$(pgrep -P 1 -f KGenericBatchMgr.class.php)
      if [[ "X$KP" = "X" && ! -f $MAINT ]]
         then
            echo "KGenericBatchMgr.class.php `hostname` was restarted"
            @APP_DIR@/scripts/serviceBatchMgr.sh restart
         fi
fi
