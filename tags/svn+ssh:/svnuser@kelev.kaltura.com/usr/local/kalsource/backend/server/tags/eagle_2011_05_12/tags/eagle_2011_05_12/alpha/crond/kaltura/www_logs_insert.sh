#!/bin/bash

cd /opt/kaltura/logs

echo -n "Working dir: "
pwd

if [ $# -eq 0 ]; then
	echo "No specific date requested, taking today"
	WHEN=$(date +%Y-%m-%d)

elif [ $# -eq 1 ]; then
	echo "You requested $1"
	WHEN=$1
else
	echo "Invalid user input"
	exit 1;
fi
# PA db alias 
DB=pa-db
SLAVEDB=pa-mysql2
DBSTATS=pa-reports
zcat /data/logs/investigate/??-apache*-access_log-$WHEN.gz |php /opt/kaltura/app/alpha/scripts/billing_summary_www.php  >www_res
php /opt/kaltura/app/alpha/scripts/billing_summary_insert.php www $WHEN www_res >www_res.sql
mysql -h${DB} kaltura -ukaltura -pkaltura < www_res.sql

rm -f www_res
rm -f www_res.sql


##cd /opt/kaltura/logs

# extract unique ips

##zcat /data/logs/investigate/??-apache*-access_log-$WHEN.gz |awk '{print $1}'|grep '^[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}'|sort -u >uvip_$WHEN

##php /opt/kaltura/app/alpha/scripts/uv_summary_insert.php ip $WHEN uvip_$WHEN > ip_res.sql
##mysql -h${DBSTATS} kaltura_stats -uroot -proot < ip_res.sql

##rm -f ip_res.sql

# extract unique cookies

##zcat /data/logs/investigate/??-apache*-access_log-$WHEN.gz |php /opt/kaltura/app/alpha/scripts/find_unique_visitors.php >uv_$WHEN

##php /opt/kaltura/app/alpha/scripts/uv_summary_insert.php cookie $WHEN uv_$WHEN > cookie_res.sql
##mysql -h${DBSTATS} kaltura_stats -uroot -proot < cookie_res.sql

##rm -f cookie_res.sql

