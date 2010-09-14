#!/bin/bash

cd /web/kaltura/alpha/batch

LOGFILE="/web/logs/$HOSTNAME-newBatchConvertClient.log"
PHP="./newBatchConvertClient.php 1"
BATCH_LOGFILE="/web/logs/$HOSTNAME-batchRunner.log"
php batchRunner.php "php $PHP" $LOGFILE >> $BATCH_LOGFILE &
PHP="./newBatchConvertClient.php 2"
php batchRunner.php "php $PHP" $LOGFILE >> $BATCH_LOGFILE &