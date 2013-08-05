This source contains:
 - The Kaltura client library (KalturaClient.py & KalturaClientBase.py)
 - Auto generated core APIs (KalturaCoreClient.py)
 - Auto generated plugin APIs (KalturaPlugins/*.py)
 - Python library test code and data files (TestCode/*)
 - The 'poster' python module (used by KalturaClient.py)

== STANDARD DEPENDENCIES ==

The API library depends on the following python modules (included with python by default):
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
 
== EXTERNAL DEPENDENCIES ==

The API library depends on the following python modules that are not included by default with python:
 - setuptools - can be downloaded from https://pypi.python.org/pypi/setuptools
 - poster - can be downloaded from https://pypi.python.org/pypi/poster/
	installed by running: python setup.py install

== INSTALLATION ==

Make sure you have the modules listed under the 'external dependencies' installed.
Install the Kaltura client by running 'python setup.py install' from the same folder as this README file.

== TESTING THE CLIENT LIBRARY ==
  
To run the test script that accompanies this source:
 - Edit your Partner ID, Service Secret, Admin Secret and User Name in 'TestCode/PythonTester.py'
 - Run "python PythonTester.py"

Note: The library was tested under ActivePython 2.5.5
