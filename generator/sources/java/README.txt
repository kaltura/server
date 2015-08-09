This is the Readme for the Kaltura Java API Client Library.
You should read this before building and testing the client.

== CONTENTS OF THIS PACKAGE ==

 - Kaltura Java Client Library API (src/main/java/com)
 - Compilation and Run test script (src/Kaltura.java)
 - JUnit tests (src/test/java/com)


== DEPENDENCIES ==
 
The API depends on these libraries:
 - Apache Commons HTTP Client 3.1 (legacy): http://hc.apache.org/downloads.cgi
 - Log4j: http://logging.apache.org/log4j/1.2/download.html
 - Apache Commons Logging 1.1: http://commons.apache.org/downloads/download_logging.cgi
 - Apache Commons Codec 1.4: http://commons.apache.org/codec/download_codec.cgi
 - JUnit 3.8.2 (optional): http://sourceforge.net/projects/junit/files/junit/



== BUILDING FROM SOURCE USING MAVEN ==

Use the following command to build the API without running unit tests:
  mvn -Dmaven.test.skip=true package

After running the command you will find 3 Jar files in the "target" directory.
  -- target/kalturaClient-X.X.X.jar contains the compiled client library
  -- target/kalturaClient-X.X.X-sources.jar contains the source code
  -- target/kalturaClient-X.X.X-javadoc.jar contains the Javadoc documentation for the library

== TESTING THE API CLIENT LIBRARY USING MAVEN ==

Edit the src/test/resources/test.properties file, enter valid data to ENDPOINT,PARTNER_ID, SECRET and ADMIN_SECRET variables.

Use the following command to both build the API and run unit tests:
  mvn package

The same Jar files will be created as above.  The results of the unit tests will be stored in the file
target/surefire-reports/com.kaltura.client.test.KalturaTestSuite.txt



== BUILDING FROM SOURCE USING ECLIPSE ==

To build the API:
 - Setup the project in eclipse.
 - Build the project


== TESTING THE API CLIENT LIBRARY USING ECLIPSE ==

To run the main class (Kaltura.java):
 - Edit the src/com/kaltura/client/test/test.properties file, enter valid data to ENDPOINT, PARTNER_ID, SECRET and ADMIN_SECRET variables.
 - Compile the client library.
 - Right click the Kaltura.java file and choose Debug As > Java Application.

To run the JUnit test suite that accompanies this source:
 - Edit the src/com/kaltura/client/test/test.properties file, enter valid data to ENDPOINT,PARTNER_ID, SECRET and ADMIN_SECRET variables.
 - Compile the client library.
 - Right click the KalturaTestSuite.java file and choose Debug As > JUnit Test.

  
== SETUP log4j LOGGING IN ECLIPSE ==

The launch settings are saved in the following files:
- 1. KalturaTestSuite.launch (the JUnit tests)
- 2. KalturaMainTest.launch (A main test class for quickly testing the build)

There is a log4j.properties file in src/test/resources/log4j. 
 - Edit it to set the log level as desired, defaults are:
  log4j.category.KalturaClientBase.class=DEBUG
  log4j.logger.com.kaltura=ERROR

