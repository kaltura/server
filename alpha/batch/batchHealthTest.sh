#!/bin/bash

cd /web/kaltura/alpha/batch

LOGFILE="/web/logs/$HOSTNAME-batchHealthTest.log"
PHP="./batchHealthTest.php"
BATCH_LOGFILE="/web/logs/$HOSTNAME-batchRunner.log"
php batchRunner.php "php $PHP" $LOGFILE >> $BATCH_LOGFILE &
