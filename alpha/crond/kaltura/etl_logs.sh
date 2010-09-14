#!/bin/bash
DWHSERV=pa-reports
WHEN=$(date +%F)
KALTURALOGS="/opt/kaltura/logs"
JOBLOG="${KALTURALOGS}/etl_job-$WHEN.log"
for serv_id in `seq 6` ; do
    echo "-----------------------------" >>$JOBLOG
    echo "pa-apache${serv_id} is processed" >>$JOBLOG
    echo "-----------------------------" >>$JOBLOG
    zcat /web/logs/investigate/pa-apache${serv_id}-access_log-$WHEN.gz |php /web/kaltura/alpha/scripts/create_event_log_from_apache_access_log.php  2>>$JOBLOG > ${KALTURALOGS}/_events_log_combined_pa-apache${serv_id}-${WHEN}
    scp ${KALTURALOGS}/_events_log_combined_pa-apache${serv_id}-${WHEN} etl@${DWHSERV}:events/_events_log_combined_pa-apache${serv_id}-${WHEN}
    ssh etl@${DWHSERV} "mv events/_events_log_combined_pa-apache${serv_id}-${WHEN} events/events_log_combined_pa-apache${serv_id}-${WHEN}"
done
