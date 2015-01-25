#!/bin/bash -e
if [ $# -lt 4 ];then
    echo "Usage: $1 </path/cli/lib/prefix> <api_hostname> <partner_id> <partner_admin_secret>"
    exit 1
fi

cd `dirname $0`
PREFIX=$1
API_HOST=$2
PARTNER_ID=$3
ADMIN_SECRET=$4
mkdir -p $PREFIX
cp -r . $PREFIX/cli
cd $PREFIX/cli
sed -i "s#@BASEDIR@#$PREFIX/cli#g" kalcliAliases.sh kalcliAutoComplete logToCli
shopt -s expand_aliases
. $PREFIX/cli/kalcliAutoComplete
. $PREFIX/cli/kalcliAliases.sh

# if we are a super user, we can symlink aliases and bash completion.
if [ `id -u` = 0 ] ;then 
    ln -sf $PREFIX/cli/kalcliAutoComplete /etc/bash_completion.d/
    ln -sf $PREFIX/cli/kalcliAliases.sh /etc/profile.d/
fi
sed  -e "s#@API_HOST@#$API_HOST#g" -e "s#@LOG_DIR@#$PREFIX/log#g" -e "s#@PARTNER_ID@#$PARTNER_ID#g" -e "s#@YOUR_ADMIN_SECRET@#$ADMIN_SECRET#g" $PREFIX/cli/config/config.template.ini > $PREFIX/cli/config/config.ini
