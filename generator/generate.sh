#!/bin/bash -e
. /etc/kaltura.d/system.ini

output_path=$WEB_DIR/content/clientlibs

rm -fr $output_path/$@*
rm -fr $APP_DIR/cache/api_v3/*
rm -fr $APP_DIR/cache/generator/*

php $APP_DIR/generator/generate.php "$@"

rsync -av --delete $output_path/php5ZendClientAdminConsole/Kaltura/Client/ $APP_DIR/admin_console/lib/Kaltura/Client
rsync -av --delete $output_path/php5ZendVarConsole/Kaltura/Client/ $APP_DIR/var_console/lib/Kaltura/Client
rsync -av --delete $output_path/batchClient/ $APP_DIR/batch/client
rsync -av --delete $output_path/php5/ $APP_DIR/tests/standAloneClient/lib
rsync -av --delete $output_path/php5 $APP_DIR/clients

rm -fr $APP_DIR/cache/batch/*
