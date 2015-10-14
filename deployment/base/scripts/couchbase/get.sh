#!/bin/bash

curl -X GET -H "Content-Type: application/json" "http://Administrator:password@127.0.0.1:8091/pools/default/buckets/ResponseProfile/docs/$1"