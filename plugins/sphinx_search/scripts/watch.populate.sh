#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../../configurations/system.ini

POPULATE_FROM_LOG="populateFromLog.php"
SERVER=$1
if [ -z "$SERVER" ];then
        echo "No Sphinx conf. Exiting."
        exit 1
fi
MAILTO="$ADMIN_CONSOLE_ADMIN_MAIL"
KP=$(pgrep -f $POPULATE_FROM_LOG)
MAINT=$BASE_DIR/maintenance
if [ -z "$KP" -a ! -f $MAINT ];then
          echo "$POPLATE_FROM_LOG `hostname` was restarted" | mail -s "Sphinx population daemon was restarted. `hostname`\nPlease check $LOG_DIR/kaltura_sphinx_populate.log\n" $MAILTO
	  cd $APP_DIR/plugins/sphinx_search/scripts
	  php $POPULATE_FROM_LOG ${SERVER} >> $LOG_DIR/kaltura_sphinx_populate.log 2>&1 &
fi

