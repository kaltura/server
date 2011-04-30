This source contains:
 - a copy of the Kaltura client library generator
 - JavaClientGenerator.php, which is an extension of ClientGeneratorFromXml.php (and a port of the .net client generator)
 - Java classes that, when combined with the generated class files, make up the Kaltura Java Client API
 - JUnit tests
 - an Ant build file

== DEPENDENCIES ==
 
The API depends on these libraries, which are not included with the distribution:
 - Apache Commons HTTP Client 3.1 (legacy): http://hc.apache.org/downloads.cgi
 - Log4j: http://logging.apache.org/log4j/1.2/download.html
 - Apache Commons Logging 1.1: http://commons.apache.org/downloads/download_logging.cgi
 - Apache Commons Codec 1.4: http://commons.apache.org/codec/download_codec.cgi
 - JUnit 3.8.2 (optional): http://sourceforge.net/projects/junit/files/junit/
 
You must also have PHP and the PHP command-line interface installed to use the client library generator.
 
== BUILDING FROM SOURCE ==

To build the API:
 - Download the dependencies and place the JARs in ./lib
 - Run "ant generate-client" to generate the Java classes that depend on the Kaltura schema
 - Run "ant compile" to compile the API
 - Run "ant jar" co create a JAR file in ./build which you can use in your applications.

== TESTING THE API ==
  
To run the JUnit test suite that accompanies this source:
 - Edit your Partner ID, Service Secret & Admin Secret under 'src\java\com\kaltura\client\tests\BaseTest.java'
 - Place a small test video called video.flv in /var/tmp (for testing upload)
 - Run "ant test" to run all JUnit tests.
 
== DEBUGGING ==

There is a log4j.properties file in the root of ./src/java (which gets copied to the root of the JAR file). Edit it to set the log level as desired.
  