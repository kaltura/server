#!/bin/bash
. /etc/kaltura.d/system.ini

echo `date`

WHEN=$(date +%Y%m%d)
php $APP_DIR/alpha/batch/updateKuserFromDWH.php >> $LOG_DIR/updateKuserFromDWH-${WHEN}.log 2>&1