This is the Readme for the Kaltura Java API Client Library.
You should read this before setting up the client in eclipse.

== CONTENTS OF THIS PACKAGE ==

 - Kaltura Java Client Library API (/src/com)
 - Compilation and Run test script (/src/Kaltura.java)
 - JUnit tests (/src/com/kaltura/client/test)
 - lib (JARs required to build the client library)


== DEPENDENCIES ==
 
The API depends on these libraries:
 - Apache Commons HTTP Client 3.1 (legacy): http://hc.apache.org/downloads.cgi
 - Log4j: http://logging.apache.org/log4j/1.2/download.html
 - Apache Commons Logging 1.1: http://commons.apache.org/downloads/download_logging.cgi
 - Apache Commons Codec 1.4: http://commons.apache.org/codec/download_codec.cgi
 - JUnit 3.8.2 (optional): http://sourceforge.net/projects/junit/files/junit/

 
== BUILDING FROM SOURCE ==

To build the API:
 - Setup the project in eclipse.
 - Build the project


== TESTING THE API CLIENT LIBRARY ==

To run the main class (Kaltura.java):
 - Edit the src/com/kaltura/client/test/test.properties file, enter valid data to ENDPOINT, PARTNER_ID, SECRET and ADMIN_SECRET variables.
 - Compile the client library.
 - Right click the Kaltura.java file and choose Debug As > Java Application.

To run the JUnit test suite that accompanies this source:
 - Edit the src/com/kaltura/client/test/test.properties file, enter valid data to ENDPOINT,PARTNER_ID, SECRET and ADMIN_SECRET variables.
 - Compile the client library.
 - Right click the KalturaTestSuite.java file and choose Debug As > JUnit Test.

To auto compile run the tests on a Linux ENV:
 - Edit the src/com/kaltura/client/test/test.properties file, enter valid data to ENDPOINT, PARTNER_ID, SECRET and ADMIN_SECRET variables.
 - run ./testAPI/test.sh

  
== SETUP log4j LOGGING IN ECLIPSE ==

The launch settings are saved in the following files:
- 1. KalturaTestSuite.launch (the JUnit tests)
- 2. KalturaMainTest.launch (A main test class for quickly testing the build)

There is a log4j.properties file in /src/log4j. 
 - Edit it to set the log level as desired.
