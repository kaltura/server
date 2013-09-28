from utils import GetConfig
from utils import KalturaBaseTest

from KalturaClient.Plugins.Core import KalturaFlavorAssetListResponse

class FlavorAssetTests(KalturaBaseTest):
     
    def test_instantiate(self):
        flavAsst = self.client.flavorAsset
        
    def test_list(self):
        pass
    
        #flavAsstList = flavAsst.list()
        ##TODO - Must set up a Filter and provide entryIdIn to properly test this.
        #self.assertIsInstance(flavAsstList, list)
        
        
        

import unittest
def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(FlavorAssetTests),
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)
