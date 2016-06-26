#!/bin/bash - 

#set -o nounset     # Treat unset variables as an error
if [ ! -x "`which jar 2>/dev/null`" ];then
    echo "Need to install jar."
    exit 1
fi
if [ ! -x "`which javac 2>/dev/null`" ];then
    echo "Need to install javac."
    exit 2
fi
if [ ! -x "`which mvn 2>/dev/null`" ];then
    echo "Need to install mvn."
    exit 3
fi
if [ "$RUN_ENV" = 'development' ];then
    BASEDIR=`dirname $0`
    if [ -r "$BASEDIR/../src/log4j/log4j.ci.properties" ]; then
        mv $BASEDIR/../src/log4j/log4j.properties $BASEDIR/../src/log4j/log4j.bk.properties
        cp $BASEDIR/../src/log4j/log4j.ci.properties $BASEDIR/../src/log4j/log4j.properties
    fi
fi

# compile
mvn -Dmaven.test.skip=true package
# test
mvn package
