#!/bin/bash

LOGDIR=/web/logs/investigate/`/bin/date +%Y/%m/%d`/partnerusage
WHEN=$(date -d "yesterday" +%Y-%m-%d)

mkdir -p $LOGDIR
php /opt/kaltura/app/alpha/scripts/batch/validatePartnerUsage.php  null 100 >> $LOGDIR/`hostname`-BatchPartnerUsage_monthlyUpgradeProcess.log.${WHEN} 2>&1
tail $LOGDIR/`hostname`-BatchPartnerUsage_monthlyUpgradeProcess.log.${WHEN} | mail -s "batchPartnerMonthlyUsage on `hostname`" production-it@kaltura.com