#!/bin/bash

WHEN=$(date -d "yesterday" +%Y-%m-%d)

php @APP_DIR@/scripts/findEntriesSizes.php $WHEN >> @LOG_DIR@/`hostname`-findEntriesSizes.log
php @APP_DIR@/alpha/batch/batchPartnerUsage.php >> @LOG_DIR@/`hostname`-BatchPartnerUsage_upgradeProcess.log 2>&1
