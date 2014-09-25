import unittest

from utils import GetConfig
from utils import KalturaBaseTest
from utils import getTestFile

from KalturaClient.Plugins.Core import KalturaUploadTokenListResponse
from KalturaClient.Plugins.Core import KalturaUploadToken

class UploadTokenTests(KalturaBaseTest):
    
    def test_list(self):
        resp = self.client.uploadToken.list()
        self.assertIsInstance(resp, KalturaUploadTokenListResponse)

    def test_Upload(self):
        token = KalturaUploadToken()
        token = self.client.uploadToken.add(token)
        self.assertIsInstance(token, KalturaUploadToken)
        self.addCleanup(self.client.uploadToken.delete, token.getId())
        
        ulFile = getTestFile('DemoVideo.flv')
        resp = self.client.uploadToken.upload(token.getId(), ulFile)
        self.assertIsInstance(resp, KalturaUploadToken)
        
        

def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(UploadTokenTests),
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)