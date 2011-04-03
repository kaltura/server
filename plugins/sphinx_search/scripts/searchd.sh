#!/bin/bash
#
# Init file for searchd
#
# chkconfig: 2345 55 25
#
# description: searchd 
#
# by Vladimir Fedorkov Mar 1, 2006, info@astellar.com 
# Slightly modified by Kaltura
#
# Source function library.
#. /etc/rc.d/init.d/functions

# public domain

BASE_PATH=@SPHINX_BIN_DIR@
PID_FILE=@BASE_DIR@/sphinx/searchd.pid
CONFIG_FILE=@APP_DIR@/configurations/sphinx/kaltura.conf

EXEC_PATH=$BASE_PATH
LOG_PATH=@LOG_DIR@/sphinx

RETVAL=0
prog="searchd"

export prog RETVAL EXEC_PATH SUDO_USER CONFIG_FILE

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

do_start() {
	echo -n $"Starting:"
	$EXEC_PATH/$prog --config $CONFIG_FILE
	RETVAL=$?
	echo
	if [ "$RETVAL" -eq 0 ]; then
		echo_success
        echo
	else
		echo_failure
        echo
	fi
	return $RETVAL
}

do_stop() {
	echo -n $"Shutting down:"
	if [ -e $PID_FILE ] ; then
		kill -15 `cat $PID_FILE`
		sleep 5
		if [ -e $PID_FILE ] ; then
			kill -9 `cat $PID_FILE`
		fi
	fi
	RETVAL=$?
	if [ "$RETVAL" -eq 0 ]; then
		echo_success
        echo
	else
		echo_failure
        echo
	fi
	return $RETVAL
}

do_stopwait() {
        echo "Stopping $prog with wait"
		$EXEC_PATH/$prog --config $CONFIG_FILE --stopwait
        RETVAL=$?
        echo
        return $RETVAL
}

show_status() {
      KP=$(pgrep -P 1 -f searchd) 
      if [ "X$KP" != "X"  ]; then
	  echo "searchd is running as $KP ..."
	  return 0
      else
          echo "Service searchd isn't running"
          return 1
      fi
}

case $* in


start)
	do_start
	;;

stop)
	do_stop
	;;
status)
    show_status
    ;;
stopwait)
        do_stopwait
        ;;

*)
	echo "usage: $0 {start|stop|stopwait|status}" >&2

	exit 1
	;;
esac

exit $RETVAL

