import unittest

from utils import GetConfig
from utils import KalturaBaseTest
from utils import getTestFile

from KalturaClient.Plugins.Core import KalturaMediaListResponse
from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaType
from KalturaClient.Plugins.Core import KalturaMediaEntryFilter
from KalturaClient.Plugins.Core import KalturaUploadToken, KalturaUploadedFileTokenResource


class MediaTests(KalturaBaseTest):
    
    def test_list(self):
        resp = self.client.media.list()
        self.assertIsInstance(resp, KalturaMediaListResponse)
        
        objs = resp.objects
        self.assertIsInstance(objs, list)
        
        [self.assertIsInstance(o, KalturaMediaEntry) for o in objs]
        
    def test_addFromUploadedFile(self):
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName('pytest.MediaTests.test_addFromUploadedFile')
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
            
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile)            
                     
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
        
        self.assertIsInstance(mediaEntry.getId(), unicode)
        
        #cleanup
        self.client.media.delete(mediaEntry.id)

    def test_updateContent(self):
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName('pytest.MediaTests.test_updateContent')
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile)
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
        self.addCleanup(self.client.media.delete, mediaEntry.getId())
        self.readyWait(mediaEntry.getId())
        
        #now, change the content on the mediaEntry to another video file
        token = KalturaUploadToken()
        token = self.client.uploadToken.add(token)
        self.addCleanup(self.client.uploadToken.delete, token.getId())
        ulFile = getTestFile('countdown.mp4')
        token = self.client.uploadToken.upload(token.getId(), ulFile)
        
        #create a resource
        resource = KalturaUploadedFileTokenResource()
        resource.setToken(token.getId())
        
        #DO THE TEST
        newMediaEntry = self.client.media.updateContent(mediaEntry.getId(), resource)
        
        #must approve it...
        newMediaEntry = self.client.media.approveReplace(newMediaEntry.getId())
        self.readyWait(newMediaEntry.getId())
        
        #make sure everything but content is the same
        self.assertEqual(mediaEntry.getId(), newMediaEntry.getId())
        self.assertEqual(mediaEntry.getName(), newMediaEntry.getName())
        
        self.assertNotEqual(mediaEntry.getDuration(), newMediaEntry.getDuration())
        
        
        
class Utf8_tests(KalturaBaseTest):
    
    test_unicode = u'\u03dd\xf5\xf6'  #an odd representation of the word 'FOO'
    
    def test_utf8_name(self):
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName(u'pytest.MediaTests.test_UTF8_name'+self.test_unicode)
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile)
        
        #this will throw an exception if fail.
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
            
        self.addCleanup(self.client.media.delete, mediaEntry.getId())
    
    def test_utf8_tags(self):        
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName('pytest.MediaTests.test_UTF8_tags')
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile)

        mediaEntry.setTags(self.test_unicode)
        
        #this will throw an exception if fail.
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
            
        self.addCleanup(self.client.media.delete, mediaEntry.getId())
        
    def test_list_utf8_search(self):
        """Get a list of videos based on a tag search containing unicode"""
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName('pytest.MediaTests.test_UTF8_tags')
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile)
        mediaEntry.setTags(self.test_unicode)
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
        self.addCleanup(self.client.media.delete, mediaEntry.getId())
        
        self.readyWait(mediaEntry.getId())
        
        #find it!
        kfilter = KalturaMediaEntryFilter()
        kfilter.setTagsLike(self.test_unicode)
        result = self.client.media.list(filter=kfilter)
        
        self.assertEqual(1, len(result.objects), 
                         msg="Did not get expected number of objects back from result")
        
        mediaResult = result.objects[0]
        self.assertEqual(mediaEntry.getId(), mediaResult.getId(),
                         msg="Did not get expected media object from result")


def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(MediaTests),
        unittest.makeSuite(Utf8_tests)
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)