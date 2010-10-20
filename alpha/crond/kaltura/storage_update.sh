#!/bin/bash

cd /opt/kaltura/logs

WHEN=$(date -d "yesterday" +%Y-%m-%d)

php /opt/kaltura/app/support_prod/test/dummy/findEntriesSizes.php $WHEN >> /var/log/`hostname`-findEntriesSizes.log 2>&1
#not in use any more
##php /opt/kaltura/app/support_prod/test/dummy/findMediaStats.php $WHEN >> /var/log/`hostname`-findMediaStats.log 2>&1
#not ready yet
#php /web/kaltura/alpha/test/dummy/findUserStats.php $WHEN
#php /web/kaltura/alpha/batch/batchPartnerUsage.php >> /web/logs/BATCH1-batchPartnerUsage.log
TODAY=$(date +%F)
#php /web/kaltura/alpha/batch/newBatchPartnerUsage.php daily_storage $TODAY >> /web/logs/BATCH1-newBatchPartnerUsage.log
#php /web/kaltura/alpha/batch/newBatchPartnerUsage.php monthly_agg $TODAY >> /web/logs/BATCH1-newBatchPartnerUsageMonthly.log
# monitor partner usage:
php /opt/kaltura/app/alpha/batch/batchPartnerUsage.php >> /var/log/`hostname`-BatchPartnerUsage_upgradeProcess.log 2>&1
