#!/bin/bash
#WGET_RC=1
#while [ $WGET_RC -ne 0 ]; do
cat /web/storage/r17/level3logs/logs/convert/www.kaltura.com/$1/* > l3_logs_$1_.log
gzip l3_logs_$1_.log
#	WGET_RC=$?
#done
