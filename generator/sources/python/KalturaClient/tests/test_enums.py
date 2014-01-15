from utils import GetConfig
from utils import KalturaBaseTest

from KalturaClient.Base import KalturaEnumsFactory

#Not sure where this is going.  It's probably really wrong

class EnumTests(KalturaBaseTest):
    
    def setUp(self):
        """No Need to setup a client connection"""
        pass

    def testMetadataObjectType(self):
        KalturaMetadataObjectType = KalturaEnumsFactory.enumFactories['KalturaMetadataObjectType']
        
        
    def tearDown(self):
        pass