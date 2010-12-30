#!/bin/bash

rm -fr ../cache/api_v3/*
rm -fr ../cache/generatorv3/*

php generate.php

cp output/adminConsoleClient/* ../admin_console/lib/Kaltura
cp output/batchClient/* ../batch/client
cp output/php5full/* ../tests/unit_test/lib