#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../../configurations/system.ini

WHEN=$(date +%Y%m%d)
php $APP_DIR/alpha/batch/updateEntryFromDWH.php >> $LOG_DIR/updateEntryFromDWH-${WHEN}.log 2>&1