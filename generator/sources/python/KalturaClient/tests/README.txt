Some (ok, most...) Tests require you to connect to a working Kaltura Server.
To specify credentials, see "secret_config.py"

Take the time to make sure your .gitignore file is correct, 
 so you don't commit your 'secret' credentials.

Almost all things that are not Kaltura in here are probably documented here: 
http://docs.python.org/2.7/library/unittest.html


To Run All Tests:

cd to the directory that contains KalturaClient (do not cd into the actual KalturaClient package)

run:
  python -m unittest discover
  
  
To run individual tests:

  python -m unittest KalturaClient.tests.TESTMODULE[.TESTCLASS][.TEST_METHOD]


--Quick Introduction--
To see an example of a very simple test case, look at test_media MediaTests.test_list
 This test will simply exercise the 'list' method off of media, and make sure that the client receives a 'MediaListResponse' object, and validates that every object in the returned list is an instance of a KalturaMediaEntry
 
Many tests will create Objects on the server for testing purposes.
 For example, the tests in test_playlist will create playlists and media entries on the remote server, then exercise these playlists and check for the existence (or lack thereof) of media entries in the playlists
 When creating objects on the remote server, you will typically see a call to 'addCleanup':
 
    self.addCleanup(<function or method>, parameters)
    
 addCleanup is a method provided by the unittest.TestCase base test, and will add a callable to be run during teardown.
 
 A test case that does this, for example:
   kplaylist = self.client.playlist.add(kplaylist)
   
 Should politely also do this:
   self.addCleanup(self.client.playlist.delete, kplaylist.getId())
   
 And when tearDown() is run, the playlist, for example, would be removed from the account and server the tests were run against.
 
 Without it, your test case will leave test (garbage) on the server - That's just not very polite.
 
