#!/bin/sh
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../configurations/system.ini

mysql -h$DWH_HOST -P$DWH_PORT -u$DWH_USER -p$DWH_PASS < $BASE_DIR/app/scripts/dwh/trigger.sql |sed -e '1d' |php $BASE_DIR/app/scripts/dwh/updateEntryPlaysViews.php
mysql -h$DWH_HOST -P$DWH_PORT -u$DWH_USER -p$DWH_PASS < $BASE_DIR/app/scripts/dwh/wrap.sql

