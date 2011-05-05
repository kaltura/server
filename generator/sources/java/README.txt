This source contains:
 - Kaltura Java Client Library API (/src/com)
 - Compilation test script (/src/Kaltura.java)
 - JUnit tests (/tests)
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

== TESTING THE API ==
  
To run the JUnit test suite that accompanies this source:
 - Edit your Partner ID, Service Secret & Admin Secret under 'src/com/kaltura/client/tests/BaseTest.java'
 
== DEBUGGING ==

There is a log4j.properties file in /src/log4j. 
Edit it to set the log level as desired.
  