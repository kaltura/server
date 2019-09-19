#!/bin/sh
. /etc/kaltura.d/system.ini

echo `date`

echo "get plays/views"
mysql -h$KAVA_DB_HOST -P$KAVA_DB_PORT -u$KAVA_DB_USER -p$KAVA_DB_PASS < $BASE_DIR/app/alpha/scripts/kava/get_plays_views.sql |sed -e '1d' |php $BASE_DIR/app/alpha/scripts/kava/updateEntryPlaysViews.php

