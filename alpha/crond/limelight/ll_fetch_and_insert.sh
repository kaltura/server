#!/bin/bash

REPORT_TO=eran.etam@kaltura.com,gonen.radai@kaltura.com,yuval.shemesh@kaltura.com,alex.bandel@kaltura.com
SCRIPT_PATH=/opt/kaltura/app/alpha/crond/limelight/

cd /opt/kaltura/logs

echo -n "Working dir: "
pwd

if [ $# -eq 0 ]; then
	echo "No specific date requested, taking 3 days back"
	WHEN=$(date -d "-3 day" +%Y%m%d)

elif [ $# -eq 1 ]; then
	echo "You requested $1"
	WHEN=$1
else
	echo "Invalid user input"
	exit 1;
fi

bash $SCRIPT_PATH/get_ll_log.sh $WHEN
GET_RC=$?
echo
if [ $GET_RC -ne 0 ]; then
	echo "Failed getting log for limelight, aborting"
	echo "Error while downloading limelight logs ($WHEN)" | mail -s "Limelight error" $REPORT_TO
	exit 2;
fi

bash $SCRIPT_PATH/ll_insert.sh $WHEN
