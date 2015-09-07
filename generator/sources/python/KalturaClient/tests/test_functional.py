# ===================================================================================================
#                           _  __     _ _
#                          | |/ /__ _| | |_ _  _ _ _ __ _
#                          | ' </ _` | |  _| || | '_/ _` |
#                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
#
# This file is part of the Kaltura Collaborative Media Suite which allows users
# to do with audio, video, and animation what Wiki platfroms allow them to do with
# text.
#
# Copyright (C) 2006-2011  Kaltura Inc.
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http:#www.gnu.org/licenses/>.
#
# @ignore
# ===================================================================================================

import os

from utils import GetConfig
from utils import getTestFile
from utils import KalturaBaseTest
from utils import KalturaLogger

from KalturaClient import *

from KalturaClient.Base import KalturaConfiguration

from KalturaClient.Plugins.Core import KalturaSessionType
from KalturaClient.Plugins.Core import KalturaMediaEntry, KalturaMediaEntryFilter, KalturaMediaEntryOrderBy
from KalturaClient.Plugins.Core import KalturaMediaType
from KalturaClient.Plugins.Core import KalturaDataEntry
from KalturaClient.Plugins.Core import KalturaException
from KalturaClient.Plugins.Core import KalturaFilterPager

from KalturaClient.Plugins.Core import API_VERSION
testString = "API Test ver %s" % (API_VERSION,)

import urllib
import time
import re

class SingleRequestTests(KalturaBaseTest):
    """These Tests Are legacy tests migrated from the first test suite TestCode/PythonTester.py
       into a unittest framework
       Great Examples to work from!
    """
    
    ### See Base Class 'setup' method for instantiating a client.
    
    def test_addmedia(self):
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = self.client.media.upload(ulFile)

        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName("Media Entry Using Python Client ver %s" % (API_VERSION,))
        mediaEntry.setMediaType(KalturaMediaType(KalturaMediaType.VIDEO))
        mediaEntry = self.client.media.addFromUploadedFile(mediaEntry, uploadTokenId)

        # serve
        DATA_ENTRY_CONTENT = 'bla bla bla'
        dataEntry = KalturaDataEntry()
        dataEntry.setName('test data entry')
        dataEntry.setDataContent(DATA_ENTRY_CONTENT)
        addedDataEntry = self.client.data.add(dataEntry)
        serveUrl = self.client.data.serve(addedDataEntry.id)
        f = urllib.urlopen(serveUrl)
        assert(DATA_ENTRY_CONTENT == f.read())
    
    
    def test_SampleMetadataOperations(self):
        
        from KalturaClient.Plugins.Metadata import KalturaMetadataProfile
        from KalturaClient.Plugins.Metadata import KalturaMetadataObjectType
        from KalturaClient.Plugins.Metadata import KalturaMetadataProfileFilter
        from KalturaClient.Plugins.Metadata import KalturaMetadataFilter        
        
        # The metadata field we'll add/update
        metaDataFieldName = "SubtitleFormat"
        fieldValue = "VobSub"
    
        # The Schema file for the field
        # Currently, you must build the xsd yourself. There is no utility provided.
        xsdFile = "MetadataSchema.xsd"
    
        # Setup a pager and search to use
        pager = KalturaFilterPager()
        search = KalturaMediaEntryFilter()
        search.setOrderBy(KalturaMediaEntryOrderBy.CREATED_AT_ASC)
        search.setMediaTypeEqual(KalturaMediaType.VIDEO)  # Video only
        pager.setPageSize(10)
        pager.setPageIndex(1)
    
        print "List videos, get the first one..."
    
        # Get 10 video entries, but we'll just use the first one returned
        entries = self.client.media.list(search, pager).objects
    
        # make sure we have a metadata profile
        profile = KalturaMetadataProfile() 
        profile.setName('TestProfile %s' % (testString,))
        MetadataObjectType = KalturaMetadataObjectType.ENTRY
        
        profile.setMetadataObjectType(MetadataObjectType)
        viewsData = ""
    
        xsdFh = getTestFile(xsdFile)
        newProfile = self.client.metadata.metadataProfile.add(profile, xsdFh.read(), viewsData)
    
        # Check if there are any custom fields defined in the KMC (Settings -> Custom Data)
        # for the first item returned by the previous listaction
        filter = KalturaMetadataProfileFilter()
        metadata = self.client.metadata.metadataProfile.list(filter, pager).objects
    
        name = entries[0].getName()
        id = entries[0].getId()
        if metadata[0].getXsd() != None:
            print "1. There are custom fields for video: " + name + ", entryid: " + id
        else:
            print "1. There are no custom fields for video: " + name + ", entryid: " + id
    
        # Add a custom data entry in the KMC  (Settings -> Custom Data)
        profile = KalturaMetadataProfile()
        profile.setName('TestProfile %s' % (testString,))
        profile.setMetadataObjectType(KalturaMetadataObjectType.ENTRY)
        viewsData = ""
    
        metadataResult = self.client.metadata.metadataProfile.update(newProfile.id, profile, xsdFh.read(), viewsData)
    
        assert(metadataResult.xsd != None)
    
        # Add the custom metadata value to the first video
        filter2 = KalturaMetadataFilter()
        filter2.setObjectIdEqual(entries[0].id)
        xmlData = "<metadata><SubtitleFormat>" + fieldValue + "</SubtitleFormat></metadata>"
        metadata2 = self.client.metadata.metadata.add(newProfile.id, profile.metadataObjectType, entries[0].id, xmlData)
    
        assert(metadata2.xml != None)
        
        print "3. Successfully added the custom data field for video: " + name + ", entryid: " + id
        xmlStr = metadata2.xml
        print "XML used: " + xmlStr
    
        # Now lets change the value (update) of the custom field
        # Get the metadata for the video
        filter3 = KalturaMetadataFilter()
        filter3.setObjectIdEqual(entries[0].id)
        filter3.setMetadataProfileIdEqual(newProfile.id)
        metadataList = self.client.metadata.metadata.list(filter3).objects
        assert(metadataList[0].xml != None)
    
        print "4. Current metadata for video: " + name + ", entryid: " + id
        xmlquoted = metadataList[0].xml
        print "XML: " + xmlquoted
        xml = metadataList[0].xml
        # Make sure we find the old value in the current metadata
        pos = xml.find("<" + metaDataFieldName + ">" + fieldValue + "</" + metaDataFieldName + ">")
        assert(pos >= 0)
    
        pattern = re.compile("<" + metaDataFieldName + ">([^<]+)</" + metaDataFieldName + ">")
        xml = pattern.sub("<" + metaDataFieldName + ">Ogg Writ</" + metaDataFieldName + ">", xml)
        rc = self.client.metadata.metadata.update(metadataList[0].id, xml)
        print "5. Updated metadata for video: " + name + ", entryid: " + id
        xmlquoted = rc.xml
        print "XML: " + xmlquoted


from utils import PARTNER_ID, SERVICE_URL
from utils import ADMIN_SECRET, USER_NAME

class MultiRequestTests(KalturaBaseTest):
    
    def setUp(self):
        """These tests require that client.session.start be used
           Instead of self.client.generateSession
           TODO: Document Why
        """
        
        self.config = GetConfig()
        self.client = KalturaClient(self.config)
        self.ks = None
                  
    
    def test_MultiRequest(self):
        """From lines 221- 241 of origional PythonTester.py"""
        
        self.client.startMultiRequest()
        ks = self.client.session.start(ADMIN_SECRET, USER_NAME, 
                                       KalturaSessionType.ADMIN, 
                                       PARTNER_ID, 86400, "") 
        self.client.setKs(ks)
        
        listResult = self.client.baseEntry.list()

        multiResult = self.client.doMultiRequest()
        print multiResult[1].totalCount
        self.client.setKs(multiResult[0])
        
        # error
        try:
            mediaEntry = self.client.media.get('invalid entry id')
            assert(False)
        except KalturaException, e:
            assert(e.code == 'ENTRY_ID_NOT_FOUND')                    
           
        # multi request error
        self.client = KalturaClient(GetConfig())

        #start a NEW multirequest (could move to separate unit test?)
        self.client.startMultiRequest()

        ks = self.client.session.start(ADMIN_SECRET, USER_NAME, 
                                            KalturaSessionType.ADMIN, 
                                            PARTNER_ID, 86400, "")
        self.client.setKs(ks)

        mediaEntry = self.client.media.get('invalid entry id')
    
        multiResult = self.client.doMultiRequest()
        self.client.setKs(multiResult[0])
        assert(isinstance(multiResult[1], KalturaException))
        assert(multiResult[1].code == 'ENTRY_ID_NOT_FOUND')    

        #must be called with existing client multirequest session
        self._AdvancedMultiRequestExample()

    # copied from C# tester
    def _AdvancedMultiRequestExample(self):
        #this is a separate, local client - not 'self.client'
        client = KalturaClient(self.config) #matches line 154 in PythonTester.py
        client.startMultiRequest()
        
        from KalturaClient.Plugins.Core import KalturaMixEntry
        from KalturaClient.Plugins.Core import KalturaEditorType
        
        # Request 1
        ks = client.session.start(ADMIN_SECRET, USER_NAME, 
                                  KalturaSessionType.ADMIN, 
                                  PARTNER_ID, 86400, "")
        client.setKs(ks) # for the current multi request, the result of the first call will be used as the ks for next calls
  
        mixEntry = KalturaMixEntry()
        mixEntry.setName(".Net Mix %s" % (testString,))
        mixEntry.setEditorType(KalturaEditorType.SIMPLE)
    
        # Request 2
        mixEntry = client.mixing.add(mixEntry)
    
        # Request 3
        ulFile = getTestFile('DemoVideo.flv')
        uploadTokenId = client.media.upload(ulFile)
    
        mediaEntry = KalturaMediaEntry()
        mediaEntry.setName("Media Entry For Mix %s" % (testString,))
        mediaEntry.setMediaType(KalturaMediaType.VIDEO)
    
        # Request 4
        mediaEntry = client.media.addFromUploadedFile(mediaEntry, uploadTokenId)
    
        # Request 5
        client.mixing.appendMediaEntry(mixEntry.id, mediaEntry.id)
    
        response = client.doMultiRequest()
    
        for subResponse in response:
            if isinstance(subResponse, KalturaException):
                self.fail("Error occurred: " + subResponse.message)
    
        # when accessing the response object we will use an index and not the response number (response number - 1)
        assert(isinstance(response[1], KalturaMixEntry))
        mixEntry = response[1]
        
        print "The new mix entry id is: " + mixEntry.id


import unittest
def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(SingleRequestTests),
        unittest.makeSuite(MultiRequestTests),
    ))
        
if __name__ == "__main__":
    suite = test_suite()
    unittest.TextTestRunner(verbosity=2).run(suite)
