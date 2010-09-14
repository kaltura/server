cd /web/kaltura/alpha/batch
if test -z "$1"
then
        id=1
else
        id=$1
fi
LOGFILE="/web/logs/$HOSTNAME-newBatchConvertServer.log"
PHP="./newBatchConvertServer.php"
BATCH_LOGFILE="/web/logs/$HOSTNAME-batchRunner.log"

# run batch server on all 8 cores
taskset -c 0 php batchRunner.php "php $PHP 0" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 1 php batchRunner.php "php $PHP 1" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 2 php batchRunner.php "php $PHP 2" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 3 php batchRunner.php "php $PHP 3" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 4 php batchRunner.php "php $PHP 4" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 5 php batchRunner.php "php $PHP 5" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 6 php batchRunner.php "php $PHP 6" $LOGFILE >> $BATCH_LOGFILE &
taskset -c 7 php batchRunner.php "php $PHP 7" $LOGFILE >> $BATCH_LOGFILE &