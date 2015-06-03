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
#all paths are relative to this bin dir
RELATIVE_BASE_DIR=../../../..
TEST_PATH=$RELATIVE_BASE_DIR/src/test
TEST_RESOURCE_PATH=$TEST_PATH/resources
MAIN_SRC=$RELATIVE_BASE_DIR/src/main/java
LIB_PATH=$RELATIVE_BASE_DIR/lib
cp $TEST_RESOURCE_PATH/DemoImage.jpg $TEST_RESOURCE_PATH/DemoVideo.flv $TEST_RESOURCE_PATH/test.properties .
javac -d . -sourcepath $MAIN_SRC -cp $LIB_PATH/commons-codec-1.4.jar:$LIB_PATH/commons-httpclient-3.1.jar:$LIB_PATH/commons-logging-1.1.1.jar:$LIB_PATH/junit-4.7.jar:$LIB_PATH/log4j-1.2.15.jar $MAIN_SRC/com/kaltura/client/test/KalturaTestSuite.java
jar cvf  kalturaClient.jar .
cd $RELATIVE_BASE_DIR
java -Dlog4j.configuration=file:src/main/resources/log4j.properties -cp lib/commons-codec-1.4.jar:lib/commons-httpclient-3.1.jar:lib/commons-logging-1.1.1.jar:lib/junit-4.7.jar:lib/log4j-1.2.15.jar:src/test/scripts/bin/kalturaClient.jar org.junit.runner.JUnitCore   com.kaltura.client.test.KalturaTestSuite

