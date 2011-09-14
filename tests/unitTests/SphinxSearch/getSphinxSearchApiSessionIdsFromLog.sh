#!/bin/bash
# usage
# ./ getSphinxSearchApiSessionIdsFromLog.sh KalturaApiV3LogFilePath SphinxSearchApiSessionIdsFromLog 
grep -v 'INSERT' ${1} |  grep -v 'SELECT COUNT(*)' | egrep ' kaltura_entry|FROM `entry` WHERE|DEBUG: Disptach took - ' >${2}

exit 0


