#!/bin/bash

# the input is YYYY-MM-DD and we shorten it to YY-MM-DD

WHEN=$1
WHEN=${WHEN:2:8}
rm -f activity_res.sql
DBHOST_PROD=@DB1_HOST@
DBNAME_PROD=@DB1_NAME@
DBHOST_STATS=@DB_STATS_HOST@
DBNAME_STATS=@DB_STATS_NAME@

echo "delete from partner_activity where activity_date='$WHEN' and activity=2;" > activity_res.sql
mysql -h${DBHOST_STATS} ${DBNAME_STATS} -uroot -proot -ss -e "select concat('insert into partner_activity values(0,',partner_id,',\'',date_format(date,'%y-%m-%d'),'\',2,202,',count(1),',0,0,0,0,0,0,0,0,0);') from collect_stats where date_format(date,'%y-%m-%d')='$WHEN' and command='view' group by partner_id" >>activity_res.sql
mysql -h${DBHOST_STATS} ${DBNAME_STATS} -uroot -proot -ss -e "select concat('insert into partner_activity values(0,',partner_id,',\'',date_format(date,'%y-%m-%d'),'\',2,201,',count(1),',0,0,0,0,0,0,0,0,0);') from collect_stats where date_format(date,'%y-%m-%d')='$WHEN' and command='play' group by partner_id" >>activity_res.sql

mysql -h${DBHOST_PROD} ${DBNAME_PROD} -uroot -proot < activity_res.sql

rm -f activity_res.sql
