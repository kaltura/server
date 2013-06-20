#!/bin/sh
. /etc/kaltura.d/system.ini

echo `date`

echo "start dwh triggers"
mysql -h$DWH_HOST -P$DWH_PORT -u$DWH_USER -p$DWH_PASS < $BASE_DIR/app/alpha/scripts/dwh/trigger.sql |sed -e '1d' |php $BASE_DIR/app/alpha/scripts/dwh/updateEntryPlaysViews.php
echo "start dwh wrap"
mysql -h$DWH_HOST -P$DWH_PORT -u$DWH_USER -p$DWH_PASS < $BASE_DIR/app/alpha/scripts/dwh/wrap.sql
echo "end dwh"
