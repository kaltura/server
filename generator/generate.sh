#!/bin/bash

rm -fr ../cache/api_v3/*
rm -fr ../cache/generatorv3/*

php generate.php "$@"

rsync -avC output/adminConsoleClient/ ../admin_console/lib/Kaltura
rsync -avC output/batchClient/ ../batch/client