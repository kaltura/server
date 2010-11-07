#!/bin/bash
MAILTO="@ADMIN_CONSOLE_ADMIN_MAIL@"
KP=$(pgrep -P 1 -f searchd)
MAINT=@BASE_DIR@/maintenance
if [ "X$KP" = "X" ]
   then
      sleep 10
      KP=$(pgrep -P 1 -f searchd)
      if [[ "X$KP" = "X" && ! -f $MAINT ]]
         then
            echo "searchd `hostname` was restarted"
            @APP_DIR@/scripts/searchd.sh start
         fi
fi
