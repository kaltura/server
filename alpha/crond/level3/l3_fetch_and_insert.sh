#!/bin/bash

REPORT_TO=eran.etam@kaltura.com,gonen.radai@kaltura.com,yuval.shemesh@kaltura.com,alex.bandel@kaltura.com
SCRIPT_PATH=/opt/kaltura/app/alpha/crond/level3/

cd /opt/kaltura/logs/level3

echo -n "Working dir: "
pwd

if [ $# -eq 0 ]; then
	echo "No specific date requested, taking 3 days back"
	WHEN=$(date -d "-3 day" +%F)

elif [ $# -eq 1 ]; then
	echo "You requested $1"
	WHEN=$1
else
	echo "Invalid user input"
	exit 1;
fi

bash $SCRIPT_PATH/get_l3_log.sh $WHEN
GET_RC=$?
echo
if [ $GET_RC -ne 0 ]; then
	echo "Failed getting log for level3, aborting"
	echo "Error while downloading level3 logs ($WHEN)" | mail -s "level3 error" $REPORT_TO
	exit 2;
fi

bash $SCRIPT_PATH/l3_insert.sh $WHEN
