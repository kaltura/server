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
# public domain

BASE_PATH=@BIN_DIR@/sphinx
PID_FILE=@BASE_DIR@/sphinx/searchd.pid
CONFIG_FILE=@APP_DIR@/configurations/sphinx/kaltura.conf

EXEC_PATH=$BASE_PATH
LOG_PATH=@LOG_DIR@/sphinx

RETVAL=0
prog="searchd"

export prog RETVAL EXEC_PATH SUDO_USER CONFIG_FILE

do_start() {
	echo "Starting $prog"
	$EXEC_PATH/$prog --config $CONFIG_FILE
	RETVAL=$?
	echo
	return $RETVAL
}

do_stop() {
	echo "Stopping $prog"
	if [ -e $PID_FILE ] ; then
		kill -15 `cat $PID_FILE`
		sleep 5
		if [ -e $PID_FILE ] ; then
			kill -9 `cat $PID_FILE`
		fi
	fi
	RETVAL=$?
	echo
	return $RETVAL
}

do_stopwait() {
        echo "Stopping $prog with wait"
		$EXEC_PATH/$prog --config $CONFIG_FILE --stopwait
        RETVAL=$?
        echo
        return $RETVAL
}

case $* in


start)
	do_start
	;;

stop)
	do_stop
	;;

stopwait)
        do_stopwait
        ;;

*)
	echo "usage: $0 {start|stop|stopwait}" >&2

	exit 1
	;;
esac

exit $RETVAL

