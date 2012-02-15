#!/bin/bash
source ../../../configurations/system.ini

nice -n 19 find /tmp/cache_v3-600 -type f -cmin +15 -name "cache*" -delete
nice -n 19 find /tmp/cache_v2 -type f -cmin +15 -name "cache*" -delete
