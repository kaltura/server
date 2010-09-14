#!/bin/bash
zcat kaltura.ht.$1.log.gz | php /opt/kaltura/app/alpha/scripts/billing_summary_limelight.php  >ll_res
php /opt/kaltura/app/alpha/scripts/billing_summary_insert.php ll $1 ll_res >ll_res.sql
mysql -hpa-db kaltura -uroot -proot < ll_res.sql

rm -f ll_res
rm -f ll_res.sql
