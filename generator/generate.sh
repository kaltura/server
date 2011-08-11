#!/bin/bash

output_path=`php -r 'require_once("bootstrap.php"); echo myContentStorage::getFSContentRootPath() . "/content/clientlibs";' 2>&1`

rm -fr $output_path/*
rm -fr ../cache/api_v3/*
rm -fr ../cache/generator/*

php generate.php "$@"

rsync -avC $output_path/php5ZendClientAdminConsole/ ../admin_console/lib
rsync -avC $output_path/batchClient/ ../batch/client
rsync -avC  $output_path/testsClient/* ../tests/lib

rm -fr ../cache/batch/*
