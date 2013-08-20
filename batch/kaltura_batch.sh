#!/bin/bash
. /etc/kaltura.d/system.ini

echo `date`

#
# batchMgr		This shell script takes care of starting and stopping a Kaltura Batch Service
#
# chkconfig: 2345 13 87
# description: Kaltura Batch

# Source function library.
#. /etc/rc.d/init.d/functions

# Directory containing the batchers' php files
BATCHDIR=$APP_DIR/batch

BATCHEXE=KGenericBatchMgr.class.php

if [ $# != 1 ]; then
	echo "Usage: $0 [start|stop|restart|status]"
	exit 0 	
fi

CONFIG_FILE=$APP_DIR/configurations/batch

LOCKFILE="$LOG_DIR/batch/batch.pid"

echo_success() {
	[ "$BOOTUP" = "color" ] && $MOVE_TO_COL
	echo -n "["
	[ "$BOOTUP" = "color" ] && $SETCOLOR_SUCCESS
	echo -n $"	OK	"
	[ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
	echo -n "]"
	echo -ne "\r"
	return 0
}

echo_failure() {
	[ "$BOOTUP" = "color" ] && $MOVE_TO_COL
	echo -n "["
	[ "$BOOTUP" = "color" ] && $SETCOLOR_FAILURE
	echo -n $"FAILED"
	[ "$BOOTUP" = "color" ] && $SETCOLOR_NORMAL
	echo -n "]"
	echo -ne "\r"
	return 0
}


start() {
	if [ -f $BASE_DIR/maintenance ]; then
		echo "Server is on maintenance mode - batchMgr will not start!"
		return 1
	fi
	
	echo -n $"Starting:"
	KP_PARENT=`ps -eo pid,args|grep $BATCHEXE -m1|grep -v grep|awk -F " " '{print $1}'|xargs`
	KP=`ps -eo pid,args|grep $BATCHEXE|grep -v grep|awk -F " " '{print $1}'|xargs`
	if ! kill -0 `cat $LOCKFILE 2>/dev/null` 2>/dev/null; then 
		echo_failure
		echo
		if [ "X$KP_PARENT" != "X" ]; then
			echo "Service batch already running [$KP_PARENT]"
			return 0
		else
			echo "Service batch isn't running but stale lock file exists"
			echo "Removing stale lock file at $LOCKFILE"
			rm -f $LOCKFILE
			start_scheduler
			return 0
		fi
	else
		if [ "X$KP" != "X" ]; then
			echo "Batch is running as $KP without a $LOCKFILE"
			return 0
		fi		
		start_scheduler
		return 0
	fi
}

start_scheduler() {
	echo "$PHP_BIN $BATCHEXE $PHP_BIN $CONFIG_FILE >> $LOG_DIR/kaltura_batch.log 2>&1 &"
	cd $BATCHDIR
	su $OS_KALTURA_USER -c "$PHP_BIN $BATCHEXE $PHP_BIN $CONFIG_FILE >> $LOG_DIR/kaltura_batch.log 2>&1 &" 
	if [ "$?" -eq 0 ]; then
		echo_success
		echo
	else
		echo_failure
		echo
	fi
}

show_status() {
		KP=`ps -eo pid,args|grep $BATCHEXE|grep -v grep|awk -F " " '{print $1}'|xargs`
		if [ "X$KP" != "X" ]; then
		echo "Batch is running as $KP ..."
		return 0
		else
			echo "Service Batch isn't running"
			return 0
		fi
}

stop() {
	echo -n $"Shutting down:"
	KP_PARENT=`ps -eo pid,args|grep $BATCHEXE|grep -v grep|awk -F " " '{print $1}'|xargs`
	KP=`ps -eo pid,args|grep $BATCHEXE|grep -v grep|awk -F " " '{print $1}'|xargs`
	if [ -n "$KP" ]; then
		if [ -f $BASE_DIR/keepAlive ]; then
			echo "Server is on Keep Alive mode - workers won't be killed!"
			kill -9 $KP_PARENT
		else
			echo "Killing batchMgr and workers"
			kill -9 $KP
		fi
		echo_success
		echo
		RC=0
	else
		echo_failure
		echo
		echo "Service Batch not running"
		#RC=2
		RC=0
	fi
	rm -f $LOCKFILE
	return $RC
}

case "$1" in
	start)
		start
		;;
	stop)
		stop
		;;
	status)
		show_status
		;;
	restart)
		stop
		start
		;;
	*)
		echo "Usage: [start|stop|restart|status]"
		exit 0
		;;
esac
exit 0
