Some (ok, most...) Tests require you to connect to a working Kaltura Server.
To specify credentials, see "secret_config_example.py"

Take the time to make sure your .gitignore file is correct, 
 so you don't commit your 'secret' credentials.


To Run All Tests:

cd to the directory that contains KalturaClient (do not cd into the actual KalturaClient package)

run:
  python -m unittest discover
  
  
To run individual tests:

  python -m unittest KalturaClient.tests.TESTMODULE[.TESTCLASS][.TEST_METHOD]


More info here:
http://docs.python.org/2.7/library/unittest.html
