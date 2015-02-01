#!/bin/bash - 
set -o nounset
if [ -r `dirname $0`/colors.sh ];then
    . `dirname $0`/colors.sh
fi
if [ $# -lt 2 ];then
    echo "Usage: $1 </path/cli/lib/prefix> <partner_id>"
    exit 1
fi
PREFIX=$1
PARTNER_ID=$2
shopt -s expand_aliases
. $PREFIX/kalcliAutoComplete
. $PREFIX/kalcliAliases.sh
PASSED=0
FAILED=0
inc_counter()
{
    VAL=$1
    if [ $VAL -eq 0 ];then
	PASSED=`expr $PASSED + 1`
    else
	FAILED=`expr $FAILED + 1`
    fi
}
TEST_FLV="`dirname $0`/DemoVideo.flv"
echo -e "${BRIGHT_BLUE}######### Running tests ###########${NORMAL}"
KS=`genks -b $PARTNER_ID`
kalcli -x media list ks=$KS
inc_counter $?
SOME_ENTRY_ID=`kalcli -x baseentry list pager:objectType=KalturaFilterPager pager:pageSize=1 filter:objectType=KalturaBaseEntryFilter   filter:typeEqual=1 ks=$KS|awk '$1 == "id" {print $2}'`
inc_counter $?
kalcli -x baseentry updateThumbnailFromSourceEntry  entryId=$SOME_ENTRY_ID sourceEntryId=$SOME_ENTRY_ID ks=$KS  timeOffset=3
inc_counter $?
kalcli -x  partner register partner:objectType=KalturaPartner partner:name=apartner partner:adminName=apartner partner:adminEmail=partner@example.com partner:description=someone cmsPassword=partner012
inc_counter $?
TOKEN=`kalcli -x uploadtoken add uploadToken:objectType=KalturaUploadToken uploadToken:fileName=$TEST_FLV  ks=$KS|awk '$1 == "id" {print $2}'`
inc_counter $?
kalcli -x uploadtoken upload fileData=@$TEST_FLV uploadTokenId=$TOKEN ks=$KS
inc_counter $?
TEST_CAT_NAM=testme012
kalcli -x category add category:objectType=KalturaCategory category:name=$TEST_CAT_NAM  ks=$KS
RC=$?
inc_counter $RC
if [ $RC -eq 0 ];then
    TOTALC=`kalcli -x category list filter:objectType=KalturaCategoryFilter filter:fullNameEqual=$TEST_CAT_NAM ks=$KS|awk '$1 == "totalCount" {print $2}'`
    if [ $TOTALC -eq 1 ];then
	inc_counter 0
    else
	inc_counter 1
    fi
    CAT_ID=`kalcli -x category list filter:objectType=KalturaCategoryFilter filter:fullNameEqual=$TEST_CAT_NAM ks=$KS|awk '$1 == "id" {print $2}'`
    kalcli -x category delete  id=$CAT_ID ks=$KS
    inc_counter $?
fi
echo -e "${BRIGHT_GREEN}PASSED tests: $PASSED ${NORMAL}, ${BRIGHT_RED}FAILED tests: $FAILED ${NORMAL}"
if [ "$FAILED" -gt 0 ];then
    exit 1
fi
