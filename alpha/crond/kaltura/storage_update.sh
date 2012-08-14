#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../../configurations/system.ini

WHEN=$(date -d "yesterday" +%Y-%m-%d)

php $APP_DIR/scripts/findEntriesSizes.php $WHEN >> $LOG_DIR/`hostname`-findEntriesSizes.log
php $APP_DIR/scripts/batch/validatePartnerUsage.php >> $LOG_DIR/`hostname`-BatchPartnerUsage_upgradeProcess.log 2>&1
