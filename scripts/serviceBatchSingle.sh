#!/bin/bash
#
#       /etc/rc.d/init.d/test
# serviceBatchSingle      This shell script takes care of starting and stopping a Kaltura Batch Service (singleton mode)
#
# Author: Amir Shaked amirshaked@gmail.com
#
# chkconfig: 2345 13 87
# description: Kaltura Batch

# Source function library.
. /etc/rc.d/init.d/functions

# Directory containing the batchers' php files
BATCH_DIR="@APP_DIR@/alpha/batch" 
#BATCH_DIR="/opt/kaltura"
LOG_DIR="@LOG_DIR@" 
# "/opt/kaltura"
RULES_PATH="@APP_DIR@"

# Contains the batchers list, with true/false values
RULES_FILE="$RULES_PATH/rules.cfg"

# The batch service filename without extensions
FILE_NAME=${0##*/}

LOCKFILE="$BATCH_DIR/$FILE_NAME"
#PIDFILE="/var/run/$FILE_NAME.pid"

check_cfg() {
	VAL=$(grep "^$FILE_NAME " $RULES_FILE | cut -d' ' -f2)
	if [ -z $VAL ]; then
		echo_failure
		echo
		echo "Missing directive for $FILE_NAME in $RULES_FILE"
		exit 2;
	elif [ $VAL = "false" ]; then
		echo_failure
		echo
		echo "Rule say don't run service $FILE_NAME"
		exit 1;
	elif [ $VAL = "true" ]; then
		return 0
	else
		echo_failure
		echo
		echo "Value isnt true/false"
		exit 2;
	fi
}

start() {
	echo -n $"Starting:"
	check_cfg
	if [ -f $LOCKFILE ]; then
		echo_failure
		echo
		echo "Service $FILE_NAME already running"
		return 1
	else
		echo "@PHP_BIN@ $BATCH_DIR/batchRunner.php "@PHP_BIN@ $BATCH_DIR/$FILE_NAME.php" $LOG_DIR/$HOSTNAME-$FILE_NAME.log >> $LOG_DIR/$HOSTNAME-batchRunner.log  2>&1 &"
		@PHP_BIN@ $BATCH_DIR/batchRunner.php "@PHP_BIN@ $BATCH_DIR/$FILE_NAME.php" $LOG_DIR/$HOSTNAME-$FILE_NAME.log >> $LOG_DIR/$HOSTNAME-batchRunner.log  2>&1 &
		if [ "$?" -eq 0 ]; then
			echo_success
			echo
			touch $LOCKFILE	
		else
			echo_failure
			echo
		fi
		return 0
	fi
}

show_pstree() {
	KP=$(pgrep -P 1 -f $FILE_NAME.php)
	if [ $KP > 1 ]; then
		echo "Service process tree:"
		pstree -pA $KP
		PIDS=$(pstree -p $KP | grep -o '[0-9]\{2,5\}')
		for pid in $PIDS
		do	
			ps -fp $pid | tail -n1
			taskset -p $pid
		done
		return 0
	else
		echo "Service $FILE_NAME isn't running"
		return 1
	fi
}

stop() {
	echo -n $"Shutting down:"
	KP=$(pgrep -P 1 -f $FILE_NAME.php)
	if [ $KP > 1 ]; then
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
		echo "Service $FILE_NAME not running"
		RC=2
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
        show_pstree
        ;;
    restart)
        stop
        start
        ;;
    *)
        echo "Usage:  [start|stop|restart|status]"
        exit 1
        ;;
esac
exit $?
