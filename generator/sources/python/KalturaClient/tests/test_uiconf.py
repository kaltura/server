from utils import GetConfig
from utils import KalturaBaseTest

from KalturaClient.Plugins.Core import KalturaUiConf, KalturaUiConfObjType, KalturaUiConfFilter
from KalturaClient.Plugins.Core import KalturaUiConfListResponse

class UiConfTests(KalturaBaseTest):
     
    def test_list(self):
        resp = self.client.uiConf.list()
        self.assertIsInstance(resp, KalturaUiConfListResponse)
        
        objs = resp.objects
        self.assertIsInstance(objs, list)
        
        for o in objs:
            self.assertIsInstance(o, KalturaUiConf)
        
    def test_get_players(self):
        filt = KalturaUiConfFilter()
        
        players = [KalturaUiConfObjType.HTML5_PLAYER, 
                   KalturaUiConfObjType.PLAYER_V3,
                   KalturaUiConfObjType.PLAYER,
                   KalturaUiConfObjType.PLAYER_SL,
                  ]
        filt.setObjTypeIn(players)
       
        resp = self.client.uiConf.list(filter=filt)
        objs = resp.objects
        
        for o in objs:
            self.assertIn(o.objType.getValue(), players)
        
     
    def test_list_templates(self):
        templates = self.client.uiConf.listTemplates()
        self.assertIsInstance(templates, KalturaUiConfListResponse)
        
        objs = templates.objects
        self.assertIsInstance(objs, list)
        
        for o in objs:
            self.assertIsInstance(o, KalturaUiConf)
        
        
        
        
        


import unittest
def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(UiConfTests),
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)