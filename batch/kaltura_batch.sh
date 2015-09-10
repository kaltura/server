#!/bin/bash

#
# batchMgr      This shell script takes care of starting and stopping a Kaltura Batch Service
#
# chkconfig: 2345 13 87
# description: Kaltura Batch

### BEGIN INIT INFO
# Provides:          kaltura-batch
# Required-Start:    $local_fs $remote_fs $network
# Required-Stop:     $local_fs $remote_fs $network
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# X-Interactive:     true
# Short-Description: Start/stop Kaltura batch server
# Description:       Control the Kaltura batch server.
### END INIT INFO


# Source function library.
. /etc/kaltura.d/system.ini

# Define variables
BATCHDIR=$APP_DIR/batch
BATCHEXE=KGenericBatchMgr.class.php
CONFIG_FILE=$APP_DIR/configurations/batch
LOCKFILE="$LOG_DIR/batch/batch.pid"

if [ $# != 1 ]; then
    echo "Usage: $0 [start|stop|force-stop|restart|status]"
    exit 0  
fi

echo_status() {
    if [ $2 -eq 0 ]; then
        echo -n "$1"
        echo -e "\t[\e[1;32m    OK    \e[0m]"
    else
        echo -n "$1"
        echo -e "\t[\e[1;31m  FAILED  \e[0m]"
    fi
}


start() {
    if [ -f $BASE_DIR/maintenance ]; then
        echo "Server is on maintenance mode - batchMgr will not start!"
        return 1
    fi
    
    KP=`ps axf | awk '!/\\_ / {b=0} /php [K]GenericBatchMgr.class.php/ {b=1} b{print $1}'|xargs`
    if [ -f "$LOCKFILE" ]; then
        KP_PARENT=`cat $LOCKFILE 2>/dev/null`
        kill -0 $KP_PARENT
        if [ $? -eq 0 ]; then
            echo_status "Service Batch Manager already running [$KP_PARENT]" 0
        return 0
    else
        echo "Service Batch Manager isn't running but stale lock file exists"
        echo "Removing stale lock file $LOCKFILE"
        rm -f $LOCKFILE
        start_scheduler
        return 1
    fi
    elif [ -n "$KP" ]; then
        echo "Batch Manager is running as $KP without $LOCKFILE"
        start_scheduler
        return 0
    else
        start_scheduler
        return 1
    fi
}

start_scheduler() {
    echo -n "Starting Batch Manager."
    cd $BATCHDIR
    echo -n "."
    su $OS_KALTURA_USER -c "nohup $PHP_BIN $BATCHEXE $PHP_BIN $CONFIG_FILE >> $LOG_DIR/kaltura_batch.log 2>&1 &"
    echo -n "."
    if [ "$?" -eq 0 ]; then
        echo ". "
        sleep 1
        KP_PARENT=`cat $LOCKFILE 2>/dev/null`
        echo_status "Batch Manager started with PID $KP_PARENT" 0
        return 0
    else
        echo ". "
        echo_status "Failed to start Batch Manager" 1
        return 1
    fi
}

show_status() {
    KP=`ps axf | awk '!/\\_ / {b=0} /php [K]GenericBatchMgr.class.php/ {b=1} b{print $1}'|xargs`
    KP_PARENT=`cat $LOCKFILE 2>/dev/null`
    if [ -n "$KP" ]; then
        echo_status "Batch Manager running with PID $KP_PARENT" 0
        return 0
    else
        echo_status "Service Batch Manager isn't running" 1
        return 1 
    fi
}

stop() {
    KP_PARENT=`cat $LOCKFILE 2>/dev/null`
    KP=`ps axf | awk '!/\\_ / {b=0} /php [K]GenericBatchMgr.class.php/ {b=1} b{print $1}'|xargs`
    if [ -n "$KP" ]; then
        if [ -f $BASE_DIR/keepAlive ]; then
            echo "Server is on Keep Alive mode - workers won't be killed!"
            echo_status "Killing Batch Manager with PID $KP_PARENT" 0
            kill $1 $KP_PARENT
        else
            echo_status "Killing Batch Manager with PID $KP_PARENT and related workers" 0
            kill $1 $KP
        fi
        rm $LOCKFILE
        RC=$?
    else
        echo_status "Service Batch Manager not running" 1
        RC=1
    fi
    return $RC
}

case "$1" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    force-stop)
        stop -9
        ;;
    status)
        show_status
        ;;
    restart)
        stop
        start
        ;;
    *)
        echo "Usage: [start|stop|force-stop|restart|status]"
        exit 0
        ;;
esac
exit $?
