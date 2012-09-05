#!/bin/bash
if [ -L $0 ];then
	REAL_SCRIPT=`readlink $0`
else
	REAL_SCRIPT=$0
fi
BASEDIR=`dirname $REAL_SCRIPT`
cd $BASEDIR
. ../configurations/system.ini

output_path=$WEB_DIR/content/clientlibs

rm -fr $output_path/$@
rm -fr ../cache/api_v3/*
rm -fr ../cache/generator/*

php generate.php "$@"

rsync -avC $output_path/php5ZendClientAdminConsole/Kaltura/Client ../admin_console/lib/Kaltura/Client
rsync -avC $output_path/php5ZendVarConsole/Kaltura/Client ../var_console/lib/Kaltura/Client
rsync -avC $output_path/php5ZendHostedPages/Kaltura/Client ../hosted_pages/lib/Kaltura/Client
rsync -avC $output_path/batchClient/ ../batch/client
rsync -avC  $output_path/testsClient/* ../tests/lib
rsync -avC  $output_path/php5 ../clients

rm -fr ../cache/batch/*
