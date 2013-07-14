#!/bin/bash
. /etc/kaltura.d/system.ini

echo `date`

WHEN=$(date +%Y%m%d)
php $APP_DIR/alpha/batch/updateEntryFromDWH.php >> $LOG_DIR/updateEntryFromDWH-${WHEN}.log 2>&1