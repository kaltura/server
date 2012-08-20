#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../../configurations/system.ini

WHEN=$(date +%Y%m%d)
php $APP_DIR/alpha/batch/updateKuserFromDWH.php >> $LOG_DIR/updateKuserFromDWH-${WHEN}.log 2>&1