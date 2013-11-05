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
import hashlib

# Service response formats
KALTURA_SERVICE_FORMAT_JSON = 1
KALTURA_SERVICE_FORMAT_XML  = 2
KALTURA_SERVICE_FORMAT_PHP  = 3

# Xml utility functions
def getXmlNodeText(xmlNode):
    if xmlNode.firstChild == None:
        return ''
    return xmlNode.firstChild.nodeValue

def getXmlNodeBool(xmlNode):
    text = getXmlNodeText(xmlNode)
    if text == '0':
        return False
    elif text == '1':
        return True
    return None

def getXmlNodeInt(xmlNode):
    text = getXmlNodeText(xmlNode)
    if text == '':
        return None
    try:
        return int(text)
    except ValueError:
        return None

def getXmlNodeFloat(xmlNode):
    text = getXmlNodeText(xmlNode)
    if text == '':
        return None
    try:
        return float(text)
    except ValueError:
        return None

def getChildNodeByXPath(node, nodePath):
    for curName in nodePath.split('/'):
        nextChild = None
        for childNode in node.childNodes:
            if childNode.nodeName == curName:
                nextChild = childNode
                break
        if nextChild == None:
            return None
        node = nextChild
    return node

# Request parameters container
class KalturaParams(object):
    def __init__(self):
        self.params = {}

    def get(self):
        return self.params

    def put(self, key, value = None):
        if value == None:
            self.params[key + '__null'] = ''
        else:
            self.params[key] = str(value)

    def update(self, props):
        self.params.update(props.get())

    def add(self, key, objectProps):
        for (curKey, curValue) in objectProps.items():
            self.put('%s:%s' % (key, curKey), curValue)

    def addObjectIfDefined(self, key, obj):
        if obj == NotImplemented:
            return
        if obj == None:
            self.put(key)
            return
        self.add(key, obj.toParams().get())

    def addArrayIfDefined(self, key, array):
        if array == NotImplemented:
            return
        if array == None:
            self.put(key)
            return
        if len(array) == 0:
            self.put('%s:-' % key, '')
        else:
            for curIndex in xrange(len(array)):
                self.addObjectIfDefined('%s:%s' % (key, curIndex), array[curIndex])

    def addStringIfDefined(self, key, value):
        if value != NotImplemented:
            self.put(key, value)

    def addIntIfDefined(self, key, value):
        if value != NotImplemented:
            self.put(key, value)

    def addStringEnumIfDefined(self, key, value):
        if value == NotImplemented:
            return
        if value == None:
            self.put(key)
            return
        if type(value) == str:
            self.addStringIfDefined(key, value)
        else:
            self.addStringIfDefined(key, value.getValue())

    def addIntEnumIfDefined(self, key, value):
        if value == NotImplemented:
            return
        if value == None:
            self.put(key)
            return
        if type(value) == int:
            self.addIntIfDefined(key, value)
        else:
            self.addIntIfDefined(key, value.getValue())

    def addFloatIfDefined(self, key, value):
        if value != NotImplemented:
            self.put(key, value)

    def addBoolIfDefined(self, key, value):
        if value == NotImplemented:
            return
        if value == None:
            self.put(key)
            return
        if value:
            self.put(key, '1')
        else:
            self.put(key, '0')

    def signature(self):
        params = self.params.items()
        params.sort()
        str = ""
        for (k, v) in params:
            str += '%s%s' % (k, v)
        return self.md5(str)

    @staticmethod
    def md5(str):
        m = hashlib.md5()
        m.update(str)
        return m.digest().encode('hex')

# Request files container
class KalturaFiles(object):
    def __init__(self):
        self.params = {}

    def get(self):
        return self.params

    def put(self, key, value):
        self.params[key] = value

    def update(self, props):
        self.params.update(props.get())

# Abstract base class for all client objects
class KalturaObjectBase(object):
    def __init__(self):
        pass

    def fromXmlImpl(self, node, propList):
        for childNode in node.childNodes:
            nodeName = childNode.nodeName
            if not propList.has_key(nodeName):
                continue
            propLoader = propList[nodeName]
            if type(propLoader) == tuple:
                (func, param) = propLoader
                loadedValue = func(childNode, param)
            else:
                func = propLoader
                loadedValue = func(childNode)
            setattr(self, nodeName, loadedValue)

    def fromXml(self, node):
        pass
    
    def toParams(self):
        result = KalturaParams()
        result.put('objectType', 'KalturaObjectBase')
        return result

# Abstract base class for all client services
class KalturaServiceBase(object):
    def __init__(self, client = None):
        self.client = client
        
    def setClient(self, client):
        self.client = client

# Exception class for server errors
class KalturaException(Exception):
    def __init__(self, message, code):
        self.code = code
        self.message = message

    def __str__(self):
        return "%s (%s)" % (self.message, self.code)

# Exception class for client errors
class KalturaClientException(Exception):
    ERROR_GENERIC = -1
    ERROR_INVALID_XML = -2
    ERROR_FORMAT_NOT_SUPPORTED = -3
    ERROR_CONNECTION_FAILED = -4
    ERROR_READ_FAILED = -5
    ERROR_INVALID_PARTNER_ID = -6
    ERROR_INVALID_OBJECT_TYPE = -7
    ERROR_RESULT_NOT_FOUND = -8
    ERROR_READ_TIMEOUT = -9
    ERROR_READ_GZIP_FAILED = -10
  
    def __init__(self, message, code):
        self.code = code
        self.message = message

    def __str__(self):
        return "%s (%s)" % (self.message, self.code)

# Client configuration class
class KalturaConfiguration(object):
    # Constructs new Kaltura configuration object
    def __init__(self, partnerId = -1, serviceUrl = "http://www.kaltura.com", logger = None):
        self.logger                     = logger
        self.serviceUrl                 = serviceUrl
        self.partnerId                  = None
        self.format                     = KALTURA_SERVICE_FORMAT_XML
        self.clientTag                  = "python:@DATE@"
        self.requestTimeout             = 120
        
        if partnerId != None and type(partnerId) != int:
            raise KalturaClientException("Invalid partner id", KalturaClientException.ERROR_INVALID_PARTNER_ID)
        self.partnerId = partnerId
        
    # Set logger to get kaltura client debug logs
    def setLogger(self, log):
        self.logger = log
        
    # Gets the logger (internal client use)
    def getLogger(self):
        return self.logger

# Client plugin interface class
class IKalturaClientPlugin(object):
    # @return KalturaClientPlugin
    @staticmethod
    def get():
        raise NotImplementedError
        
    # @return array<KalturaServiceBase>
    def getServices(self):
        raise NotImplementedError
        
    # @return string
    def getName(self):
        raise NotImplementedError
        
# Client plugin base class
class KalturaClientPlugin(IKalturaClientPlugin):
    pass

# Kaltura enums factory
class KalturaEnumsFactory(object):
    enumFactories = {}

    @staticmethod
    def create(enumValue, enumType):
        if not KalturaEnumsFactory.enumFactories.has_key(enumType):
            raise KalturaClientException("Unrecognized enum '%s'" % enumType, KalturaClientException.ERROR_INVALID_OBJECT_TYPE)
        return KalturaEnumsFactory.enumFactories[enumType](enumValue)

    @staticmethod
    def createInt(enumNode, enumType):
        enumValue = getXmlNodeInt(enumNode)
        if enumValue == None:
            return None
        return KalturaEnumsFactory.create(enumValue, enumType)

    @staticmethod
    def createString(enumNode, enumType):
        enumValue = getXmlNodeText(enumNode)
        if enumValue == '':
            return None
        return KalturaEnumsFactory.create(enumValue, enumType)

    @staticmethod
    def registerEnums(objs):
        KalturaEnumsFactory.enumFactories.update(objs)

# Kaltura objects factory
class KalturaObjectFactory(object):
    objectFactories = {}

    @staticmethod
    def create(objectNode, expectedType):
        objTypeNode = getChildNodeByXPath(objectNode, 'objectType')
        if objTypeNode == None:
            return None
        objType = getXmlNodeText(objTypeNode)
        if not KalturaObjectFactory.objectFactories.has_key(objType):
            raise KalturaClientException("Unrecognized object '%s'" % objType, KalturaClientException.ERROR_INVALID_OBJECT_TYPE)
        result = KalturaObjectFactory.objectFactories[objType]()
        if not isinstance(result, expectedType):
            raise KalturaClientException("Unexpected object type '%s'" % objType, KalturaClientException.ERROR_INVALID_OBJECT_TYPE)
        result.fromXml(objectNode)
        return result

    @staticmethod
    def createArray(arrayNode, expectedElemType):
        results = []
        for arrayElemNode in arrayNode.childNodes:
            results.append(KalturaObjectFactory.create(arrayElemNode, expectedElemType))
        return results

    @staticmethod
    def registerObjects(objs):
        KalturaObjectFactory.objectFactories.update(objs)

# Implement to get Kaltura Client logs
class IKalturaLogger(object):
    def log(self, msg):
        raise NotImplementedError
