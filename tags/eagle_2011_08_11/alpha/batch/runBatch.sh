#!/bin/bash
# $1 is the action (start|stop|restart)
# $2 is the batch name (optional) 
cd $(dirname $0)
source kaltura_env.sh
$PHP_PATH runBatch.php $1 $2
