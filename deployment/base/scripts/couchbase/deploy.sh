#!/bin/bash
. /etc/kaltura.d/system.ini

curl -X POST -u Administrator:password -d name=ResponseProfile -d ramQuotaMB=256 -d authType=none -d proxyPort=11212 -d replicaNumber=0 'http://127.0.0.1:8091/pools/default/buckets'
curl -X POST -u Administrator:password -d name=responseProfileInvalidation -d ramQuotaMB=1024 -d authType=none -d proxyPort=11213 -d replicaNumber=0 'http://127.0.0.1:8091/pools/default/buckets'
curl -X PUT -H 'Content-Type: application/json' 'http://Administrator:password@127.0.0.1:8092/ResponseProfile/_design/deploy1' -d @$APP_DIR/deployment/base/scripts/couchbase/views.ddoc
