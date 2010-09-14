#!/bin/bash

cd /web/kaltura/alpha/batch

LOGFILE="/web/logs/$HOSTNAME-batchEmailServer.log"
PHP="./batchEmailServer.php"
BATCH_LOGFILE="/web/logs/$HOSTNAME-batchRunner.log"
php batchRunner.php "php $PHP" $LOGFILE >> $BATCH_LOGFILE &
