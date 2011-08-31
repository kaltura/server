#!/bin/bash

php @APP_DIR@/alpha/batch/updateEntryFromDWH.php >> @LOG_DIR@/`hostname`-updateEntryFromDWH.log 2>&1