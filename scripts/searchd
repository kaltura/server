#!/bin/bash
#
# Init file for searchd
#
# chkconfig: 2345 55 25
#
# description: searchd 
#
# USE "chkconfig --add searchd" to configure Sphinx searchd service
#
# by Vladimir Fedorkov Mar 1, 2006, info@astellar.com
# public domain

SUDO_USER=searchd

BASE_PATH=/opt/kaltura/sphinx
PID_FILE=$BASE_PATH/searchd.pid
CONFIG_FILE=/opt/kaltura/app/configurations/sphinx/kaltura.conf

EXEC_PATH=$BASE_PATH
LOG_PATH=$EXEC_PATH

RETVAL=0
prog="searchd"

export prog RETVAL EXEC_PATH SUDO_USER CONFIG_FILE
do_config() {
	mkdir -p $EXEC_PATH
#	mkdir $EXEC_PATH/data
	mkdir -p $LOG_PATH
	chown -R $SUDO_USER $EXEC_PATH
	chown -R $SUDO_USER $CONFIG_FILE
	chown -R $SUDO_USER $LOG_PATH

	chmod 600 $EXEC_PATH/$CONFIG_FILE
	chmod u+rwx $EXEC_PATH/*
#	chmod -R u+rw,go-rwx $EXEC_PATH/data
	chmod -R u+rw,go-rwx $LOG_PATH
}

do_start() {
	echo "Starting $prog"
	#/usr/bin/sudo -u $SUDO_USER $EXEC_PATH/bin/$prog --config $CONFIG_FILE
	su $SUDO_USER -c '$EXEC_PATH/bin/$prog --config $CONFIG_FILE'
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
        #sudo -u $SUDO_USER $EXEC_PATH/bin/$prog --config $CONFIG_FILE --stopwait
	su $SUDO_USER -c '$EXEC_PATH/bin/$prog --config $CONFIG_FILE --stopwait'
        RETVAL=$?
        echo
        return $RETVAL
}

case $* in

config)
	do_config
	;;

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
	echo "usage: $0 {start|stop|stopwait|config}" >&2

	exit 1
	;;
esac

exit $RETVAL

