#!/bin/bash
. /etc/kaltura.d/system.ini

echo `date`

php $APP_DIR/alpha/batch/updateViewsAndPlays.php >> $LOG_DIR/`hostname`-updateViewsAndPlays.log 2>&1