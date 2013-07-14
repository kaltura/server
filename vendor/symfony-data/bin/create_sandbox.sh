#!/bin/sh

# creates a symfony sandbox for this symfony version

echo ">>> initialization"
DIR=../`dirname $0`
SANDBOX_NAME=sf_sandbox
APP_NAME=frontend
PHP=php

echo ">>> project initialization"
rm -rf ${SANDBOX_NAME}
mkdir ${SANDBOX_NAME}
cd ${SANDBOX_NAME}

echo ">>> create a new project and a new app"
${PHP} ${DIR}/../../data/bin/symfony init-project ${SANDBOX_NAME}
${PHP} symfony init-app ${APP_NAME}

echo ">>> add LICENSE"
cp ${DIR}/../../LICENSE LICENSE

echo ">>> add README"
cp ${DIR}/../../data/data/SANDBOX_README README

echo ">>> add symfony command line for windows users"
cp ${DIR}/../../data/bin/symfony.bat symfony.bat

echo ">>> freeze symfony"
${PHP} symfony freeze
rm config/config.php.bak

echo ">>> default to sqlite (propel.ini)"
sed -i '' -e "s#\(propel.database *= *\)mysql#\1sqlite#" config/propel.ini
sed -i '' -e "s#\(propel.database.createUrl *= *\).*#\1sqlite://./../../../../data/sandbox.db#" config/propel.ini
sed -i '' -e "s#\(propel.database.url *= *\).*#\1sqlite://./../../../../data/sandbox.db#" config/propel.ini

echo ">>> default to sqlite (databases.yml)"
echo "all:
  propel:
    class:      sfPropelDatabase
    param:
      phptype:  sqlite
      database: %SF_DATA_DIR%/sandbox.db
" > config/databases.yml

echo ">>> add some empty files in empty directories"
touch apps/${APP_NAME}/modules/.sf apps/${APP_NAME}/i18n/.sf doc/.sf web/images/.sf
touch log/.sf cache/.sf batch/.sf data/sql/.sf data/model/.sf
touch data/symfony/generator/sfPropelAdmin/default/skeleton/templates/.sf
touch data/symfony/generator/sfPropelAdmin/default/skeleton/validate/.sf
touch data/symfony/modules/default/config/.sf
touch lib/model/.sf plugins/.sf web/js/.sf
touch test/unit/.sf test/functional/.sf test/functional/${APP_NAME}/.sf
touch web/uploads/assets/.sf

touch data/sandbox.db
chmod 777 data
chmod 777 data/sandbox.db

echo ">>> create archive"
cd ..
tar zcpf ${SANDBOX_NAME}.tgz ${SANDBOX_NAME}

echo ">>> cleanup"
rm -rf ${SANDBOX_NAME}
