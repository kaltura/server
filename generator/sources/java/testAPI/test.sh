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

# compile
mvn -Dmaven.test.skip=true package
# test
mvn package
