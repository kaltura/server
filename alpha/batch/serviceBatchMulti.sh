#!/bin/bash
#
# serviceBatchMulti      This shell script takes care of starting and stopping a Kaltura Batch Service (multiprocess mode)
#
# Author: Amir Shaked amirshaked@gmail.com
#
# chkconfig: 2345 13 87
# description: Kaltura Batch

source kaltura_env.sh

# Directory containing the batchers' php files
BATCH_DIR="$KALTURA_ROOT_DIR/kaltura/alpha/batch" 
LOG_DIR="$KALTURA_ROOT_DIR/logs" 

# Contains the batchers list, with true/false values
RULES_FILE="$KALTURA_BATCH_RULES_PATH"

# The batch service filename without extensions
FILE_NAME=${0##*/}

if [ -z $2 ]; then
	FILE_NAME=$2;
fi

LOCKFILE="$KALTURA_BATCH_LOCK_PATH/$FILE_NAME"

echo_success() {
  [ "$BOOTUP" = "color" ] && $MOVE_TO_COL
  echo -n "["
  [ "$BOOTUP" = "color" ] && $SETCOLOR_SUCCESS
  echo -n $"  OK  "
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
  return 1
}


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
		sh $BATCH_DIR/$FILE_NAME.sh &
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

show_big_pstree() {
	echo "Service process tree:"
	KP=$(pgrep -P 1 -f $FILE_NAME)
	echo "$KP"
	for p_pid in $KP
	do
		echo "Info for child $p_pid:"
		if [ $p_pid > 1 ]; then		
			pstree -pA $p_pid
			PIDS=$(pstree -p $p_pid | grep -o '[0-9]\{2,5\}')
			for pid in $PIDS
			do	
				ps -fp $pid | tail -n1
				taskset -p $pid
			done
		else
			echo "Service $FILE_NAME isn't running"
		fi
		echo
	done
	return 0
}

stop() {
	echo -n $"Shutting down:"
	KP=$(pgrep -P 1 -f $FILE_NAME)
	for p_pid in $KP
	do
		if [ $p_pid > 1 ]; then
			PIDS=$(pstree -p $p_pid | grep -o '[0-9]\{2,5\}')
			for pid in "$PIDS"
			do
				kill -9 $pid
			done
		fi
	done
	
	echo_success
	echo
	RC=0

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
        show_big_pstree
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
