import os, sys, inspect
import unittest
import ConfigParser

from KalturaClient import KalturaClient, KalturaConfiguration
from KalturaClient.Base import KalturaObjectFactory, KalturaEnumsFactory
from KalturaClient.Base import IKalturaLogger

from KalturaClient.Plugins.Core import KalturaSessionType
from KalturaClient.Plugins.Core import KalturaMediaType

dir = os.path.dirname(__file__)
filename = os.path.join(dir, 'config.ini')

config = ConfigParser.ConfigParser()
config.read(filename)
PARTNER_ID = config.getint("Test", "partnerId")
SERVICE_URL = config.get("Test", "serviceUrl")
SECRET = config.get("Test", "secret")
ADMIN_SECRET = config.get("Test", "adminSecret")
USER_NAME = config.get("Test", "userName")

import logging
logging.basicConfig(level = logging.DEBUG,
                    format = '%(asctime)s %(levelname)s %(message)s',
                    stream = sys.stdout)

class KalturaLogger(IKalturaLogger):
    def log(self, msg):
        logging.info(msg)

def GetConfig():
    config = KalturaConfiguration(PARTNER_ID)
    config.serviceUrl = SERVICE_URL
    config.setLogger(KalturaLogger())
    return config

def getTestFile(filename, mode='rb'):
    testFileDir = os.path.dirname(os.path.abspath(inspect.getfile(inspect.currentframe())))
    return file(testFileDir+'/'+filename, mode)
    
    

class KalturaBaseTest(unittest.TestCase):
    """Base class for all Kaltura Tests"""
    #TODO  create a client factory as to avoid thrashing kaltura with logins...
    
    def setUp(self):
        #(client session is enough when we do operations in a users scope)
        self.config = GetConfig()
        self.client = KalturaClient(self.config)
        self.ks = self.client.generateSession(ADMIN_SECRET, USER_NAME, 
                                             KalturaSessionType.ADMIN, PARTNER_ID, 
                                             86400, "")
        self.client.setKs(self.ks)            
            
            
    def tearDown(self):
        
        #do cleanup first, probably relies on self.client
        self.doCleanups()
        
        del(self.ks)
        del(self.client)
        del(self.config)
        
