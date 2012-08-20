#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../../configurations/system.ini

php $APP_DIR/alpha/batch/updateViewsAndPlays.php >> $LOG_DIR/`hostname`-updateViewsAndPlays.log 2>&1