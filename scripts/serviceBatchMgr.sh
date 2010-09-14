#!/bin/bash
#
# serviceBatchMgr      This shell script takes care of starting and stopping a Kaltura Batch Service
#
# chkconfig: 2345 13 87
# description: Kaltura Batch

# Source function library.
#. /etc/rc.d/init.d/functions

# Directory containing the batchers' php files
BATCHDIR=/opt/kaltura/app/batch
LOGDIR="/var/log" 

BATCHEXE=KGenericBatchMgr.class.php
# The batch service filename without extensions
FILE_NAME=${BATCHEXE%.*}

if [ $# != 1 ]; then
   echo "Usage: $0 [start|stop|restart|status]"
   exit 1 	
fi

CONFIG_FILE=$BATCHDIR/config/`hostname`_config.ini

default_config=0
if [ ! -f $CONFIG_FILE ]; then
     CONFIG_FILE=$BATCHDIR/batch_config.ini
     default_config=1
fi

LOCKFILE="/var/lock/subsys/$FILE_NAME"

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


start() {
	echo -n $"Starting:"
	KP=$(pgrep -P 1 -f $FILE_NAME.php)
	if [ -f $LOCKFILE ]; then
		echo_failure
		echo
		if [ "X$KP" != "X"  ]; then
		   echo "Service $FILE_NAME already running"
		   return 1
                else
		   echo "Service $FILE_NAME isn't running but stale lock file exists"
	           echo "Removing stale lock file at $LOCKFILE"
                   rm -f $LOCKFILE
		   start_scheduler
	           return 0
                fi
	else
		if [ "X$KP" != "X"  ]; then
          	    echo "$FILE_NAME.php is running as $KP without a $LOCKFILE"
          	    exit 0
		fi		
		start_scheduler
		return 0
	fi
}

start_scheduler() {
		echo "php $BATCHEXE php $CONFIG_FILE >> ${LOGDIR}/KGenericBatchMgr.log 2>&1 &"
                if [ $default_config -eq 1 ]; then
                   echo "Warning : using default batch_config.ini !"
                fi
                cd $BATCHDIR
		php $BATCHEXE php $CONFIG_FILE >> ${LOGDIR}/KGenericBatchMgr.log 2>&1 &
                if [ "$?" -eq 0 ]; then
                        echo_success
                        echo
                        touch $LOCKFILE
                else
                        echo_failure
                        echo
                fi
}

show_status() {
      KP=$(pgrep -P 1 -f $FILE_NAME.php) 
      if [ "X$KP" != "X"  ]; then
	  echo "$FILE_NAME.php is running as $KP ..."
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
        show_status
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
