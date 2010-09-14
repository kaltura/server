#!/bin/bash
cat inserts.sql |awk -Fvalues '{print $2}'|awk -F, '{print substr($1,2)}'|sort|uniq -c |sort -nr|less
