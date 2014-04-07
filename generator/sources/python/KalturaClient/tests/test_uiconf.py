import re

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
        
        players = [
                   KalturaUiConfObjType.PLAYER_V3,
                   KalturaUiConfObjType.PLAYER,
                   KalturaUiConfObjType.PLAYER_SL,
                  ]
        filt.setObjTypeIn(players)
       
        resp = self.client.uiConf.list(filter=filt)
        objs = resp.objects
        
        for o in objs:
            self.assertIn(o.objType.getValue(), players)
        
    def test_get_playlist_players(self):
        """Until I find a better way... this gets all uiconfs that are 'playlist players'
           not sure if this is the right way"""
        filt = KalturaUiConfFilter()
        players = [
                   KalturaUiConfObjType.PLAYER_V3,
                   KalturaUiConfObjType.PLAYER,
                   KalturaUiConfObjType.PLAYER_SL,
                  ]
        tags = 'playlist'
        
        filt.setObjTypeIn(players)
        filt.setTagsMultiLikeOr(tags)
       
        resp = self.client.uiConf.list(filter=filt)
        objs = resp.objects
        
        for o in objs:
            self.assertIn(o.objType.getValue(), players)
            match = re.search('isPlaylist="(.*?)"', o.getConfFile())
            self.assertIsNotNone(match, "isPlaylist not found in confFile")
            
            value = match.group(1)
            self.assertIn(value, ["true", "multi"])
            
    def test_get_video_players(self):
        """Until I find a better way... this gets all uiconfs that are 'single video' players
           Not sure if this is the right way"""
        filt = KalturaUiConfFilter()
        players = [KalturaUiConfObjType.PLAYER_V3,
                   KalturaUiConfObjType.PLAYER,
                   KalturaUiConfObjType.PLAYER_SL,
                  ]
        tags = 'player'
        
        filt.setObjTypeIn(players)
        filt.setTagsMultiLikeOr(tags)
       
        resp = self.client.uiConf.list(filter=filt)
        objs = resp.objects
        
        for o in objs:
            self.assertIn(o.objType.getValue(), players)
            match = re.search('isPlaylist="(.*?)"', o.getConfFile())
            if match is None:
                pass
            else:
                value = match.group(1)
                self.assertIn(value, ["true", "multi"])
                
            
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