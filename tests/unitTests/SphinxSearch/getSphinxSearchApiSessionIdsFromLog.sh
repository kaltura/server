#!/bin/bash
# usage
# ./ getSphinxSearchApiSessionIdsFromLog.sh KalturaApiV3LogFilePath SphinxSearchApiSessionIdsFromLog 
#cat ${1} | grep kaltura_entry | grep -v 'INSERT' >${2}
#grep -v 'INSERT' ${1} | grep ' kaltura_entry' >${2}
grep -v 'INSERT' ${1} |  grep -v 'SELECT COUNT(*)' | egrep ' kaltura_entry|FROM `entry` WHERE|DEBUG: Disptach took - ' >${2}

exit 0


