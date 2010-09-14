#!/bin/bash
zcat l3_logs_$1_.log.gz | php /opt/kaltura/app/alpha/scripts/billing_summary_level3.php  >l3_res
php /opt/kaltura/app/alpha/scripts/billing_summary_insert.php l3 $1 l3_res >l3_res.sql
mysql -hpa-db kaltura -uroot -proot < l3_res.sql

rm -f l3_res
rm -f l3_res.sql
