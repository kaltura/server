#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
. `dirname $REAL_SCRIPT`/../../../configurations/system.ini

find $WEB_DIR/content/uploads -ctime +7 -delete
find $WEB_DIR/content/webcam -ctime +7 -delete
find $WEB_DIR/content/preconvert -ctime +7 -delete
find $WEB_DIR/content/imports -ctime +7 -delete
find $WEB_DIR/content/new_preconvert/ -ctime +7 -delete

find $WEB_DIR/conversions/preconvert -ctime +7 -delete

find $WEB_DIR/conversions/handled -ctime +7 -delete
find $WEB_DIR/conversions/postconvert_res -ctime +7 -delete
find $WEB_DIR/conversions/download_res -ctime +7 -delete
find $WEB_DIR/conversions/preconvert_cmd -ctime +7 -delete
find $WEB_DIR/conversions/preconvert_commercial_cmd -ctime +7 -delete
