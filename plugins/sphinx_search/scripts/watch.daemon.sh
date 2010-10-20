#!/bin/bash
MAILTO="@ADMIN_CONSOLE_ADMIN_MAIL@"
MAINT=@BASE_DIR@/maintenance
SUDO_USER=searchd

BASE_PATH=@BIN_DIR@
PID_FILE=@BASE_DIR@/sphinx/searchd.pid
CONFIG_FILE=@APP_DIR@/configurations/sphinx/kaltura.conf

while /bin/true ; do
#    echo "Sleeping 10 seconds ..."
    sleep 10 
    KP=$(pgrep searchd)
    KF=$(find /usr/local/var/data -type f -name "binlog*" -mmin -3)
    ##if [[ "X$KP" == "X" || "X$KF" == "X" ]] && [[ ! -f $MAINT ]]
    if [[ "X$KP" == "X" && ! -f $MAINT ]]
      then
          if [ -f @BASE_DIR@/searchd_is_not_running_email ]
             then 
                 echo "searchd on  `hostname` is not running" | mail -r root@`hostname` -s "searchd service not found on `hostname`" $MAILTO
		 rm -f @BASE_DIR@/searchd_is_not_running_email
          fi
	  touch @BASE_DIR@/searchd_is_running_email
          echo "`date` searchd on  `hostname` was restarted" 
	  sudo -u $SUDO_USER $BASE_PATH/searchd --config $CONFIG_FILE --stopwait
	  echo "Exit code for stop was $?"
	  sudo -u $SUDO_USER $BASE_PATH/searchd --config $CONFIG_FILE
          sleep 2
	  continue          
    fi
    if [ -f @BASE_DIR@/searchd_is_running_email ]
       then
           echo "searchd on  `hostname` is running" | mail -r root@`hostname` -s "searchd service is found on `hostname`" $MAILTO
	   rm -f @BASE_DIR@/searchd_is_running_email
    fi
    touch @BASE_DIR@/searchd_is_not_running_email
done
