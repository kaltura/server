#!/bin/bash

cd @LOG_DIR@

WHEN=$(date -d "yesterday" +%Y-%m-%d)

php @APP_DIR@/scripts/findEntriesSizes.php $WHEN >> @LOG_DIR@/`hostname`-findEntriesSizes.log

