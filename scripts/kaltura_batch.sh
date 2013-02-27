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
	#exit 1 chages the return code because this fails the build
	exit 0 	
fi

CONFIG_FILE=$APP_DIR/configurations/batch.ini

LOCKFILE="$LOG_DIR/batch.pid"

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
	#return 1
	return 0
}


start() {
	if [ -f $BASE_DIR/maintenance ]; then
		echo "Server is on maintenance mode - batchMgr will not start!"
		exit 1
	fi
	
	echo -n $"Starting:"
	KP=$(pgrep -P 1 -f $BATCHEXE)
	if ! kill -0 `cat $LOCKFILE 2>/dev/null` 2>/dev/null; then 
		echo_failure
		echo
		if [ "X$KP" != "X" ]; then
			echo "Service batch already running"
			#return 1
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
			exit 0
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
		echo $$ > $LOCKFILE
	else
		echo_failure
		echo
	fi
}

show_status() {
		KP=$(pgrep -P 1 -f $BATCHEXE) 
		if [ "X$KP" != "X" ]; then
		echo "Batch is running as $KP ..."
		return 0
		else
			echo "Service Batch isn't running"
			#return 1
			return 0
		fi
}

stop() {
	echo -n $"Shutting down:"
	KP=$(pgrep -P 1 -f $BATCHEXE)
	if [ -n "$KP" ]; then
		PIDS=$(pstree -p $KP | grep -o '[0-9]\{2,5\}')
		# hack, returnds the PIDS as string and tells kill to kill all at once
		for pid in "$PIDS"
		do
			kill -9 $pid
		done
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
		#exit 1
		exit 0
		;;
esac
exit $?
