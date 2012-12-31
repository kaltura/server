#!/bin/sh
#
# sphinx searchd Free open-source SQL full-text search engine
#
# chkconfig:   - 20 80
# description: Starts and stops the sphinx searchd daemon that handles \
#	       all search requests.

### BEGIN INIT INFO
# Provides: searchd
# Required-Start: $local_fs $network
# Required-Stop: $local_fs $network
# Default-Start: 
# Default-Stop: 0 1 2 3 4 5 6
# Short-Description: start and stop sphinx searchd daemon
# Description: Sphinx is a free open-source SQL full-text search engine     
### END INIT INFO


if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
SYSTEM_INI_FILE=`dirname $REAL_SCRIPT`/../configurations/system.ini``
if [ -r "$SYSTEM_INI_FILE" ];then
    . $SYSTEM_INI_FILE
else
    echo "I could not source $SYSTEM_INI_FILE. Exiting."
    exit 1
fi
# Source function library.
. $APP_DIR/scripts/functions
prog="searchd"
config="$APP_DIR/configurations/sphinx/kaltura.conf"
exec="$BASE_DIR/bin/sphinx/searchd"


start() {
    [ -x $exec ] || exit 5
    [ -f $config ] || exit 6
    echo -n $"Starting $prog: "
    # if not running, start it up here, usually something like "daemon $exec"
    daemon $exec --config $config
    retval=$?
    echo
    return $retval
}

stop() {
    echo -n $"Stopping $prog: "
    # stop it here, often "killproc $prog"
    $exec --config $config --stopwait 
    retval=$?
    echo
    [ $retval -eq 0 ]
    return $retval
}

restart() {
    stop
    start
}

reload() {
    restart
}

force_reload() {
    restart
}

rh_status() {
    # run checks to determine if the service is running or use generic status
    status $prog
}

rh_status_q() {
    rh_status >/dev/null 2>&1
}


case "$1" in
    start)
        rh_status_q && exit 0
        $1
        ;;
    stop)
        rh_status_q || exit 0
        $1
        ;;
    restart)
        $1
        ;;
    reload)
        rh_status_q || exit 7
        $1
        ;;
    force-reload)
        force_reload
        ;;
    status)
        rh_status
        ;;
    condrestart|try-restart)
        rh_status_q || exit 0
        restart
        ;;
    *)
        echo $"Usage: $0 {start|stop|status|restart|condrestart|try-restart|reload|force-reload}"
        exit 2
esac
exit $?
