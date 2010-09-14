#!/bin/bash
#WGET_RC=1
#while [ $WGET_RC -ne 0 ]; do
wget -c "ftp://kaltura:n9iub4@kaltura.logs.llnw.net/ht/kaltura.ht.$1.log.gz"
#	WGET_RC=$?
#done
