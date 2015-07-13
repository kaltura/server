#!/bin/bash

WHEN=$(date -d "yesterday" +%Y-%m-%d)

php /opt/kaltura/app/scripts/batch/validatePartnerUsage.php null 100 >> /var/log/`hostname`-BatchPartnerUsage_monthlyUpgradeProcess.log.${WHEN} 2>&1
tail /var/log/`hostname`-BatchPartnerUsage_monthlyUpgradeProcess.log.${WHEN} | mail -s "batchPartnerUsage on `hostname`" it.prod@kaltura.com,records@kaltura.com
