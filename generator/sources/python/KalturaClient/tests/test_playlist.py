from utils import GetConfig
from utils import KalturaBaseTest

from KalturaClient.Plugins.Core import KalturaPlaylist, KalturaPlaylistType
from KalturaClient.Plugins.Core import KalturaPlaylistListResponse

class PlaylistTests(KalturaBaseTest):
     
    def test_instantiate(self):
        playlist = self.client.playlist
        
    def test_list(self):
        resp = self.client.playlist.list()
    
        self.assertIsInstance(resp, KalturaPlaylistListResponse)
                
        objs = resp.objects
        self.assertIsInstance(objs, list)
        
        [self.assertIsInstance(o, KalturaPlaylist) for o in objs] 
        
    def test_createRemote(self):
        kplaylist = KalturaPlaylist()
        kplaylist.setName('pytest.PlaylistTests.test_createRemote')
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.STATIC_LIST)) #??? STATIC LIST ???
        
        kplaylist = self.client.playlist.add(kplaylist)        
        self.assertIsInstance(kplaylist, KalturaPlaylist)
        
        self.assertIsInstance(kplaylist.getId(), unicode)
        
        #cleanup
        self.client.playlist.delete(kplaylist.getId())
        

import unittest
def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(PlaylistTests),
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)
