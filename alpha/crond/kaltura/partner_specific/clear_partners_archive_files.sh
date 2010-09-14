#!/bin/bash

# delete archive content of partner 34149
MAX_DATE=$(date -d "yesterday" +%Y-%m-%d)
/opt/kaltura/app/alpha/crond/kaltura/partner_specific/remove_archive_for_partner.sh 34149 $MAX_DATE >> /var/log/`hostname`-removePartenrsArchiveContent.log

# delete archive content of partner 27121
#/opt/kaltura/app/alpha/crond/kaltura/partner_specific/remove_archive_for_partner.sh 27121 $MAX_DATE >> /var/log/`hostname`-removePartenrsArchiveContent.log
