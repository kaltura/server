import time

from utils import GetConfig
from utils import KalturaBaseTest
from utils import getTestFile

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
        
    #def test_listEntries(self):
    #    playlistId = '1_qv2ed7vm'
    #    kplaylist = self.client.playlist.get(playlistId)
    #    assertIsInstance(kplaylist.playlistContent, unicode)
    #    assertIsInstance(kplaylist.playlistContent.split(','), list)
        
        
        
    def test_update(self):
        referenceId = 'pytest.PlaylistTests.test_update'
        
        kplaylist = KalturaPlaylist()
        kplaylist.setName(referenceId)
        kplaylist.setReferenceId(referenceId)
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.STATIC_LIST))
        kplaylist = self.client.playlist.add(kplaylist)        
        self.addCleanup(self.client.playlist.delete, kplaylist.getId())

        newPlaylist = KalturaPlaylist()        
        newPlaylist.setReferenceId(referenceId)
        newPlaylist.setName("changed!")
        self.client.playlist.update(kplaylist.getId(), newPlaylist)
        
        resultPlaylist = self.client.playlist.get(kplaylist.getId())
        self.assertEqual("changed!", resultPlaylist.getName())
        
    def test_updateStaticContent(self):        
                
        from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
        
        mediaEntry1 = KalturaMediaEntry()
        mediaEntry1.setName('pytest.PlaylistTests.test_updateStaticContent1')
        mediaEntry1.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile) 
        mediaEntry1 = self.client.media.addFromUploadedFile(mediaEntry1, uploadTokenId)
        
        self.addCleanup(self.client.media.delete, mediaEntry1.getId())
                
        mediaEntry2 = KalturaMediaEntry()
        mediaEntry2.setName('pytest.PlaylistTests.test_updateStaticContent2')
        mediaEntry2.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile) 
        mediaEntry2 = self.client.media.addFromUploadedFile(mediaEntry2, uploadTokenId)        
        
        self.addCleanup(self.client.media.delete, mediaEntry2.getId())
        
        #playlistContent is simply a comma separated string of id's ?  
        playlistContent = u','.join([mediaEntry1.getId(), mediaEntry2.getId()])
                        
        kplaylist = KalturaPlaylist()
        kplaylist.setName('pytest.PlaylistTests.test_updateStaticContent')
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.STATIC_LIST))
        
        kplaylist.setPlaylistContent(playlistContent)
        kplaylist = self.client.playlist.add(kplaylist)
        
        self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        #fetch the playlist from server and test it's content.
        resultPlaylist = self.client.playlist.get(kplaylist.getId())
        self.assertEqual(resultPlaylist.playlistContent, playlistContent)
        
        #import pdb; pdb.set_trace()  #go check your server
        
        
    def test_addStaticToExistingEmpty(self):
        from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
        referenceId = 'pytest.PlaylistTests.test_addStaticToExistingEmpty'
        #create empty playlist on server
        kplaylist = KalturaPlaylist()
        kplaylist.setName(referenceId)
        kplaylist.setReferenceId(referenceId)
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.STATIC_LIST))
        kplaylist = self.client.playlist.add(kplaylist)
        self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        playlistId = kplaylist.getId()
        
        #now, add some media
        
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName(referenceId)
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile) 
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)         
        self.addCleanup(self.client.media.delete, mediaEntry.getId())
        
        #add to (update) existing playlist
        newplaylist = KalturaPlaylist()
        newplaylist.setReferenceId(referenceId)
    
        playlistContent = u','.join([mediaEntry.getId()])
        newplaylist.setPlaylistContent(playlistContent)
        
        self.client.playlist.update(playlistId, newplaylist)
        
        #check it.
        resultPlaylist = self.client.playlist.get(playlistId)
        
        self.assertEqual(playlistContent, resultPlaylist.getPlaylistContent())
        
        
        
    def test_updateExceptionReferenceIdNotSet(self):
        from KalturaClient.Base import KalturaException
        
        kplaylist = KalturaPlaylist()
        kplaylist.setName('pytest.PlaylistTests.test_updateExceptionReferenceIdNotSet')
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.STATIC_LIST))
        kplaylist = self.client.playlist.add(kplaylist)
        self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        playlistId = kplaylist.getId()
        
        playlist = self.client.playlist.get(playlistId)
        
        #don't set referenceId
        
        self.assertRaises(KalturaException, self.client.playlist.update, playlistId, playlist) 
    
class DynamicPlaylistTests(KalturaBaseTest):
    
    def test_createRemote(self):
        kplaylist = KalturaPlaylist()
        kplaylist.setName('pytest.PlaylistTests.test_createRemote')
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.DYNAMIC))
        
        #must add a totalResults field
        kplaylist.setTotalResults(10)
        
        kplaylist = self.client.playlist.add(kplaylist)        
        self.assertIsInstance(kplaylist, KalturaPlaylist)
        
        self.assertIsInstance(kplaylist.getId(), unicode)
        
        #cleanup
        self.client.playlist.delete(kplaylist.getId())        
        
    #def test_createTagRule(self):
        #from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
        #from KalturaClient.Plugins.Core import KalturaMediaEntryFilterForPlaylist
        
        #referenceId = 'pytest.DynamicPlaylistTests.test_createTagRule'
        
        ##create a video, and put a tag on it.
        #mediaEntry = KalturaMediaEntry()
        #mediaEntry.setName(referenceId)
        #mediaEntry.setReferenceId(referenceId)
        #mediaEntry.setTags('footag')
        #mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        #ulFile = getTestFile('DemoVideo.flv')
        #uploadTokenId = self.client.media.upload(ulFile) 
        #mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
        #self.addCleanup(self.client.media.delete, mediaEntry.getId())
        
        ##create a playlist
        #kplaylist = KalturaPlaylist()
        #kplaylist.setName(referenceId)
        #kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.DYNAMIC))
        #kplaylist.setTotalResults(10)
        #kplaylist.setReferenceId(referenceId)
        
        ##create a filter for the playlist
        #playlistFilter = KalturaMediaEntryFilterForPlaylist()
        #playlistFilter.setTagsMultiLikeOr('footag')

        #filtersArray = [playlistFilter,]
        
        #kplaylist.setFilters(filtersArray)

        #kplaylist = self.client.playlist.add(kplaylist)
        #self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        #print "Waiting for Media Entry to be 'Ready'"
        #sleeptime=5
        #mediaEntry = self.client.media.get(mediaEntry.getId())
        #while mediaEntry.getStatus().getValue() != '2':
            #print "media entry status is %s " % (mediaEntry.getStatus().getValue())
            #time.sleep(sleeptime)
            #mediaEntry = self.client.media.get(mediaEntry.getId())
        
        #results = self.client.playlist.execute(kplaylist.getId(), kplaylist)
        
        #self.assertEqual(len(results), 1)
        #self.assertEqual(results[0].getName(), referenceId)
        
    #def test_createSingleCategoryRule(self):
        #from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
        #from KalturaClient.Plugins.Core import KalturaPlaylistFilter
        #referenceId = 'pytest.DynamicPlaylistTests.test_createSingleCategoryRule'
                
        #categories = "category1"        
                
        ##create a video, and assign it to a category.
        #mediaEntry = KalturaMediaEntry()
        #mediaEntry.setName(referenceId)
        #mediaEntry.setReferenceId(referenceId)
        #mediaEntry.setCategories(categories)
        #mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        #ulFile = getTestFile('DemoVideo.flv')
        #uploadTokenId = self.client.media.upload(ulFile) 
        #mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)         
        #self.addCleanup(self.client.media.delete, mediaEntry.getId())    
        
        ##create a playlist
        #kplaylist = KalturaPlaylist()
        #kplaylist.setName(referenceId)
        #kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.DYNAMIC))
        #kplaylist.setTotalResults(10)
        #kplaylist.setReferenceId(referenceId)
        
        ##Create A Filter
        #kFilter = KalturaPlaylistFilter()
        #kFilter.setCategoriesFullNameIn(categories)
        #kplaylist.setFilters([kFilter])
        
        #kplaylist = self.client.playlist.add(kplaylist)
        #self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        #print "Waiting for Media Entry to be 'Ready'"
        #sleeptime=5
        #mediaEntry = self.client.media.get(mediaEntry.getId())
        #while mediaEntry.getStatus().getValue() != '2':
            #print "media entry status is %s " % (mediaEntry.getStatus().getValue())
            #time.sleep(sleeptime)
            #mediaEntry = self.client.media.get(mediaEntry.getId())
        
        #results = self.client.playlist.execute(kplaylist.getId(), kplaylist)
        
        #self.assertEqual(len(results), 1)
        #self.assertEqual(results[0].getName(), referenceId)

    def test_createAncestorCategoryRule(self):
        from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
        from KalturaClient.Plugins.Core import KalturaPlaylistFilter
        from KalturaClient.Plugins.Core import KalturaCategory
        
        referenceId = 'pytest.DynamicPlaylistTests.test_createAncestorCategoryRule'
                
        #create category entry hierarchy
        topCategory = KalturaCategory()
        topCategory.setName("TopCategory")
        topCategory = self.client.category.add(topCategory)
        self.addCleanup(self.client.category.delete, topCategory.getId())
        
        subCategory = KalturaCategory()
        subCategory.setName("SubCategory")
        subCategory.setParentId(topCategory.getId())
        subCategory = self.client.category.add(subCategory)
        self.addCleanup(self.client.category.delete, subCategory.getId())
                
        #create a video, and assign it to subCategory.
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName(referenceId)
        mediaEntry.setReferenceId(referenceId)
        mediaEntry.setCategoriesIds(subCategory.getId())
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile) 
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)         
        self.addCleanup(self.client.media.delete, mediaEntry.getId())    
        
        #create a playlist
        kplaylist = KalturaPlaylist()
        kplaylist.setName(referenceId)
        kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.DYNAMIC))
        kplaylist.setTotalResults(10)
        kplaylist.setReferenceId(referenceId)
        
        #Create A Filter - use Top Level Category
        kFilter = KalturaPlaylistFilter()
        kFilter.setCategoryAncestorIdIn(topCategory.getId())
        kplaylist.setFilters([kFilter])
        
        kplaylist = self.client.playlist.add(kplaylist)
        self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        print "Waiting for Media Entry to be 'Ready'"
        sleeptime=5
        mediaEntry = self.client.media.get(mediaEntry.getId())
        while mediaEntry.getStatus().getValue() != '2':
            print "media entry status is %s " % (mediaEntry.getStatus().getValue())
            time.sleep(sleeptime)
            mediaEntry = self.client.media.get(mediaEntry.getId())
        
        results = self.client.playlist.execute(kplaylist.getId(), kplaylist)
        
        self.assertEqual(len(results), 1)
        self.assertEqual(results[0].getName(), referenceId)
        
    #def test_EditAncestorCategoryRule(self):
        
        #from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
        #from KalturaClient.Plugins.Core import KalturaPlaylistFilter
        #from KalturaClient.Plugins.Core import KalturaCategory
        
        #referenceId = 'pytest.DynamicPlaylistTests.test_EditAncestorCategoryRule'
                
        ##create category entry hierarchy
        #topCategory = KalturaCategory()
        #topCategory.setName("TopCategory")
        #topCategory = self.client.category.add(topCategory)
        #self.addCleanup(self.client.category.delete, topCategory.getId())
        
        #subCategory = KalturaCategory()
        #subCategory.setName("SubCategory")
        #subCategory.setParentId(topCategory.getId())
        #subCategory = self.client.category.add(subCategory)
        #self.addCleanup(self.client.category.delete, subCategory.getId())
        
        #subCategory2 = KalturaCategory()
        #subCategory2.setName("SubCategory2")
        #subCategory2.setParentId(topCategory.getId())
        #subCategory2 = self.client.category.add(subCategory2)
        #self.addCleanup(self.client.category.delete, subCategory2.getId())        
                
        ##create a video, and assign it to subCategory.
        #mediaEntry = KalturaMediaEntry()
        #mediaEntry.setName(referenceId)
        #mediaEntry.setReferenceId(referenceId)
        #mediaEntry.setCategoriesIds(subCategory.getId())
        #mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        #ulFile = getTestFile('DemoVideo.flv')
        #uploadTokenId = self.client.media.upload(ulFile) 
        #mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)         
        #self.addCleanup(self.client.media.delete, mediaEntry.getId())
        
        ##create another, assign it to subCategory2
        #mediaEntry2 = KalturaMediaEntry()
        #mediaEntry2.setName(referenceId+"2")
        #mediaEntry2.setReferenceId(referenceId+"2")
        #mediaEntry2.setCategoriesIds(subCategory2.getId())
        #mediaEntry2.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        #ulFile = getTestFile('DemoVideo.flv')
        #uploadTokenId = self.client.media.upload(ulFile) 
        #mediaEntry2 = self.client.media.addFromUploadedFile(mediaEntry2, uploadTokenId)         
        #self.addCleanup(self.client.media.delete, mediaEntry2.getId())
        
        
        ##create a playlist
        #kplaylist = KalturaPlaylist()
        #kplaylist.setName(referenceId)
        #kplaylist.setPlaylistType(KalturaPlaylistType(KalturaPlaylistType.DYNAMIC))
        #kplaylist.setTotalResults(10)
        #kplaylist.setReferenceId(referenceId)
        
        ##Create A Filter - use Top Level Category
        #kFilter = KalturaPlaylistFilter()
        #kFilter.setCategoryAncestorIdIn(topCategory.getId())
        #kplaylist.setFilters([kFilter])
        
        #kplaylist = self.client.playlist.add(kplaylist)
        #self.addCleanup(self.client.playlist.delete, kplaylist.getId())
        
        #print "Waiting for Media Entry to be 'Ready'"
        #sleeptime=5
        #mediaEntry, mediaEntry2 = (self.client.media.get(mediaEntry.getId()), 
                                   #self.client.media.get(mediaEntry2.getId()))
        #while mediaEntry.getStatus().getValue() != '2' \
              #and mediaEntry2.getStatus().getValue() != '2':
            #print "media entry status is %s, %s " % (mediaEntry.getStatus().getValue(),
                                                     #mediaEntry2.getStatus().getValue() )
            #time.sleep(sleeptime)
            #mediaEntry, mediaEntry2 = (self.client.media.get(mediaEntry.getId()), 
                                   #self.client.media.get(mediaEntry2.getId()))
            
        #results = self.client.playlist.execute(kplaylist.getId(), kplaylist)
        
        ##test existing Rule
        #self.assertEqual(len(results), 2)
        
        ##import pdb; pdb.set_trace()
        ####Edit filter to only search for SubCategory2 now.
        #new_kFilter = KalturaPlaylistFilter()
        #new_kFilter.setCategoryAncestorIdIn(subCategory2.getId())
        #new_kplaylist = KalturaPlaylist()
        #new_kplaylist.setReferenceId(referenceId)
        #new_kplaylist.setFilters([new_kFilter])
        #result_kplaylist = self.client.playlist.update(kplaylist.getId(), new_kplaylist)
        
        ##import pdb; pdb.set_trace()
        #results = self.client.playlist.execute(result_kplaylist.getId(), kplaylist)
        
        #self.assertEqual(len(results), 1)
        #self.assertEqual(results[0].getName(), referenceId+'2')
        
        
        
import unittest
def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(PlaylistTests),
        unittest.makeSuite(DynamicPlaylistTests),
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)
