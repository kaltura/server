#!/bin/bash

LOGDIR=/web/logs/investigate/`/bin/date +%Y/%m/%d`/partnerusage
WHEN=$(date -d "yesterday" +%Y-%m-%d)

mkdir -p $LOGDIR
php /opt/kaltura/app/alpha/scripts/batch/validatePartnerUsage.php  null 100 >> $LOGDIR/`hostname`-BatchPartnerUsageDeveloper_UpgradeProcess.log.${WHEN} 2>&1
tail $LOGDIR/`hostname`-BatchPartnerUsageDeveloper_UpgradeProcess.log.${WHEN} | mail -s "batchPartnerUsageDeveloper on `hostname`" production-it@kaltura.com