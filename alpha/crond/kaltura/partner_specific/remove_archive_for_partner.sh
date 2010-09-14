#!/bin/bash

PHP_SCRIPT_PATH=/web/kaltura/support_prod/test/dummy/deletePartnerArchiveContent.php

if [ -z "$1" ]; then
    echo "Must provide partner ID";
    echo "Usage: "
    echo $0 "PID DATE [--dry-run]"
    exit 1;
fi

PARTNER_ID=$1

if [ -z "$2" ]; then
    echo "No specific date requested, taking week ago"
    WHEN=$(date -d "week ago" +%Y-%m-%d)
else
    # assume php will validate date format
    WHEN=$2
fi

if [ -z "$2" ]; then
    DRYRUN=""
else
    DRYRUN=$3
fi

echo "running "$PHP_SCRIPT_PATH $PARTNER_ID $WHEN $DRYRUN
php $PHP_SCRIPT_PATH $PARTNER_ID $WHEN $DRYRUN 