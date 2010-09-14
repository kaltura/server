#!/bin/bash

cd /opt/kaltura/logs

echo -n "Working dir: "
pwd

if [ $# -eq 0 ]; then
	echo "No specific date requested, taking today"
	WHEN=$(date +%Y-%m-%d)
	WHEN2=$(date -d "yesterday" +%Y-%m-%d)

#elif [ $# -eq 1 ]; then
#	echo "You requested $1"
#	WHEN=$1
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


cd /opt/kaltura/logs

# extract unique ips

zcat /data/logs/investigate/??-apache*-access_log-$WHEN.gz |awk '{print $1}'|grep '^[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}'|sort -u >uvip_$WHEN

php /opt/kaltura/app/alpha/scripts/uv_summary_insert.php ip $WHEN uvip_$WHEN > ip_res.sql
mysql -h${DBSTATS} kaltura_stats -uroot -proot < ip_res.sql

rm -f ip_res.sql

# extract unique cookies

zcat /data/logs/investigate/??-apache*-access_log-$WHEN.gz |php /opt/kaltura/app/alpha/scripts/find_unique_visitors.php >uv_$WHEN

php /opt/kaltura/app/alpha/scripts/uv_summary_insert.php cookie $WHEN uv_$WHEN > cookie_res.sql
mysql -h${DBSTATS} kaltura_stats -uroot -proot < cookie_res.sql

rm -f cookie_res.sql

# extract collect stats

zcat /data/logs/investigate/??-apache*-access_log-$WHEN.gz |php /opt/kaltura/app/alpha/scripts/analyze_collect_stats.php >collect_res.sql

## cat collect_res.sql | cut -f2 -d"(" | cut -f1 -d")" | tr -d "'" > collect_res.sql.csv

## mysql -h${DB} kaltura -uroot -proot -e "load data local infile 'collect_res.sql.csv' into table collect_stats fields terminated by ',' lines terminated by '\n';"

mysql -h${DBSTATS} kaltura_stats -uroot -proot < collect_res.sql

rm -f collect_res.sql

# update partner_activity
/opt/kaltura/app/alpha/scripts/update_partner_activity.sh $WHEN2

# update entry views and plays
SQLDATE=`mysql -h${SLAVEDB} kaltura -ukaltura -pkaltura -s -N -e "select max(created_at) from entry where views>0"`
echo $SQLDATE

LOGDATE=${SQLDATE// /-}
LOGDATE=${LOGDATE//:/-}

echo $LOGDATE

# make a backup 
mysql -h${SLAVEDB} -ukaltura -pkaltura kaltura -e "select id,views,plays from entry" >all_entries-$LOGDATE

mysql -h${DBSTATS} -uroot -proot kaltura_stats -ss -e "select entry_id,sum(command='view'),sum(command='play') from collect_stats where date>'$SQLDATE' and entry_id<>'' group by entry_id;" > inc_entry-$LOGDATE

cat inc_entry-$LOGDATE| awk 'BEGIN {print "#!/bin/bash"} {print "mysql -hpa-db kaltura -proot -e \x27update entry set views=views+\""$2"\",plays=plays+\""$3"\" where id=\""$1"\";\x27"}'>inc-$LOGDATE.sh
chmod +x inc-$LOGDATE.sh
./inc-$LOGDATE.sh
