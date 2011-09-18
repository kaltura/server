#!/bin/bash
source ../../../configurations/system.ini

WHEN=$(date +%Y%m%d)
php $APP_DIR/alpha/batch/updateKuserFromDWH.php >> $LOG_DIR/updateKuserFromDWH-${WHEN}.log 2>&1