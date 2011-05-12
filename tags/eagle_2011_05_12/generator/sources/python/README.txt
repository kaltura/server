This source contains:
 - The Kaltura client library (KalturaClient.py & KalturaClientBase.py)
 - Auto generated core APIs (KalturaCoreClient.py)
 - Auto generated plugin APIs (KalturaPlugins/*.py)
 - Python library test code and data files (TestCode/*)
 - The 'poster' python module (used by KalturaClient.py)

== DEPENDENCIES ==

The API library depends on the following builtin python libraries:
 - email.header
 - hashlib
 - httplib
 - mimetypes
 - os
 - re
 - socket
 - sys
 - time
 - urllib
 - urllib2
 - uuid or random & sha
 - xml.dom
 - xml.parsers.expat
 
== TESTING THE CLIENT LIBRARY ==
  
To run the test script that accompanies this source:
 - Edit your Partner ID, Service Secret, Admin Secret and User Name in 'TestCode/PythonTester.py'
 - Run "python PythonTester.py"

Note: The library was tested under ActivePython 2.5.5
