#!/bin/bash
SERVER=$1
MAILTO="alex.bandel@kaltura.com"
KP=$(pgrep -f populateFromLog.php)
MAINT=/opt/kaltura/maintenance
if [[ "X$KP" = "X" && ! -f $MAINT ]]
      then
          echo "populateFromLog.php `hostname` was restarted" | mail -s "populateFromLog.php script not found on `hostname`" $MAILTO
	  cd /opt/kaltura/app/scripts/sphinx
	  php populateFromLog.php ${SERVER} >> /var/log/kaltura_sphinx_populate.log 2>&1 &
      fi

