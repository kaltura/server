#!/bin/bash

php @APP_DIR@/alpha/batch/updateKuserFromDWH.php >> @LOG_DIR@/`hostname`-updateKuserFromDWH.log 2>&1