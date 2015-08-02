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

mkdir -p bin
cd bin
cp ../src/DemoImage.jpg ../src/DemoVideo.flv ../src/test.properties .
javac -d . -sourcepath ../src -cp ../lib/commons-codec-1.4.jar:../lib/commons-httpclient-3.1.jar:../lib/commons-logging-1.1.1.jar:../lib/junit-4.7.jar:../lib/log4j-1.2.15.jar ../src/com/kaltura/client/test/KalturaTestSuite.java
jar cvf  kalturaClient.jar .
cd ../
echo "log4j.logger.com.kaltura=DEBUG" >> src/log4j/log4j.properties
java -Dlog4j.configuration=file:src/log4j/log4j.properties -cp lib/commons-codec-1.4.jar:lib/commons-httpclient-3.1.jar:lib/commons-logging-1.1.1.jar:lib/junit-4.7.jar:lib/log4j-1.2.15.jar:bin/kalturaClient.jar org.junit.runner.JUnitCore   com.kaltura.client.test.KalturaTestSuite

