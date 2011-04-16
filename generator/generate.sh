#!/bin/bash

rm -fr ../cache/api_v3/*
rm -fr ../cache/generatorv3/*

php generate.php "$@"

rsync -avC /web/content/generator/output/php5ZendClientAdminConsole/ ../admin_console/lib
rsync -avC /web/content/generator/output/batchClient/ ../batch/client

rm -fr ../cache/batch/*
