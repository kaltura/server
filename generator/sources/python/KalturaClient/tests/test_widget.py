from utils import GetConfig
from utils import KalturaBaseTest

from KalturaClient.Plugins.Core import KalturaWidgetListResponse

class WidgetTests(KalturaBaseTest):
     
    def test_list_widgets(self):
        widgets = self.client.widget.list()
        self.assertIsInstance(widgets, KalturaWidgetListResponse)


import unittest
def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(WidgetTests),
        ))

if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)
    
    