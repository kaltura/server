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
from Plugins.Core import *
from Base import *
from xml.parsers.expat import ExpatError
from xml.dom import minidom
from threading import Timer
from StringIO import StringIO
import hashlib
import random
import base64
import socket
import urllib
import types
import gzip
import time
import os

from poster.streaminghttp import register_openers
from poster.encode import multipart_encode
import urllib2

try:
    from Crypto import Random
    from Crypto.Cipher import AES
except ImportError:
    pass            # PyCrypto is required only for creating KS V2

from KalturaClient.Plugins.Core import KalturaClientConfiguration
from KalturaClient.Plugins.Core import KalturaRequestConfiguration

# Register the streaming http handlers with urllib2
register_openers()

class MultiRequestSubResult(object):
    def __init__(self, value):
        self.value = value
    def __str__(self):
        return '{%s}' % self.value
    def __repr__(self):
        return '{%s}' % self.value
    def __getattr__(self, name):
        if name.startswith('__') or name.endswith('__'):
            raise AttributeError
        return MultiRequestSubResult('%s:%s' % (self.value, name))
    def __getitem__(self, key):
        return MultiRequestSubResult('%s:%s' % (self.value, key))

class PluginServicesProxy(object):
    def addService(self, serviceName, serviceClass):
        setattr(self, serviceName, serviceClass)

class KalturaClient(object):
    RANDOM_SIZE = 16

    FIELD_EXPIRY =              '_e'
    FIELD_TYPE =                '_t'
    FIELD_USER =                '_u'

    def __init__(self, config):
        self.config = None
        self.shouldLog = False
        self.multiRequestReturnType = None
        self.callsQueue = []
        self.requestHeaders = {}
        self.clientConfiguration = {}
        self.requestConfiguration = {}

        self.config = config
        logger = self.config.getLogger()
        if (logger):
            self.shouldLog = True

        self.loadPlugins()
        self.loadConfigurations()

    def loadConfigurationItem(self, configurationMap, property):
        ucfirst = property[0].upper() + property[1:]
        setter = lambda self, value: configurationMap.update({property: value})
        setattr(self, 'set' + ucfirst, types.MethodType(setter, self))
        getter = lambda self: configurationMap[property]
        setattr(self, 'get' + ucfirst, types.MethodType(getter, self))

    def loadConfiguration(self, configurationClass, configurationMap):
        for property in configurationClass.PROPERTY_LOADERS:
            self.loadConfigurationItem(configurationMap, property)

    def loadConfigurations(self):
        self.loadConfiguration(KalturaClientConfiguration, self.clientConfiguration)
        self.loadConfiguration(KalturaRequestConfiguration, self.requestConfiguration)
        
    def loadPlugins(self):
        pluginFiles = ['Core']
        pluginsFolder = os.path.normpath(os.path.join(os.path.dirname(__file__), 'Plugins'))
        if os.path.isdir(pluginsFolder):
            for fileName in os.listdir(pluginsFolder):
                (pluginFile, fileExt) = os.path.splitext(fileName)
                if fileExt.lower() != '.py':
                    continue
                pluginFiles.append(pluginFile)

        for pluginFile in pluginFiles:
            self.loadPlugin(pluginFile)

    def loadPlugin(self, pluginFile):
        moduleHierarchy = ['KalturaClient', 'Plugins', pluginFile]
        pluginModule = __import__('.'.join(moduleHierarchy))
        for curModule in moduleHierarchy[1:]:
            pluginModule = getattr(pluginModule, curModule)

        if pluginFile == 'Core':
            pluginClass = 'KalturaCoreClient'
        else:
            pluginClass = 'Kaltura%sClientPlugin' % pluginFile
        if not pluginClass in dir(pluginModule):
            return
        
        pluginClassType = getattr(pluginModule, pluginClass)

        plugin = pluginClassType.get()
        if not isinstance(plugin, IKalturaClientPlugin):
            return

        self.registerPluginServices(plugin)
        self.registerPluginObjects(plugin)

    def registerPluginServices(self, plugin):
        pluginName = plugin.getName()
        if pluginName != '':
            pluginProxy = PluginServicesProxy()
            setattr(self, pluginName, pluginProxy)

        for (serviceName, serviceFactory) in plugin.getServices().items():
            serviceClass = serviceFactory(self)
            if pluginName == '':
                self.addCoreService(serviceName, serviceClass)
            else:
                pluginProxy.addService(serviceName, serviceClass)

    def registerPluginObjects(self, plugin):
        KalturaEnumsFactory.registerEnums(plugin.getEnums())
        KalturaObjectFactory.registerObjects(plugin.getTypes())

    def addCoreService(self, serviceName, serviceClass):
        setattr(self, serviceName, serviceClass)

    def getServeUrl(self):
        if len(self.callsQueue) != 1:
            return None

        (result, params, _) = self.getRequestParams()

        # reset state
        self.callsQueue = []

        if params != None:
            result += '?' + urllib.urlencode(params.get())
        self.log("Returned url [%s]" % result)
        return result        
        
    def queueServiceActionCall(self, service, action, returnType, params = KalturaParams(), files = KalturaFiles()):
        for param in self.requestConfiguration:
            if isinstance(self.requestConfiguration[param], KalturaObjectBase):
                params.addObjectIfDefined(param, self.requestConfiguration[param])
            else:
                params.put(param, self.requestConfiguration[param])
                
        call = KalturaServiceActionCall(service, action, params, files)
        if(self.multiRequestReturnType != None):
            self.multiRequestReturnType.append(returnType)
        self.callsQueue.append(call)

    def getRequestParams(self):
        params = KalturaParams()
        files = KalturaFiles()
        for param in self.clientConfiguration:
            params.put(param, self.clientConfiguration[param])
        params.put("format", self.config.format)
        url = self.config.serviceUrl + "/api_v3"
        if (self.multiRequestReturnType != None):
            url += "/service/multirequest"
            i = 0
            for call in self.callsQueue:
                callParams = call.getParamsForMultiRequest(i)
                callFiles = call.getFilesForMultiRequest(i)
                params.update(callParams)
                files.update(callFiles)
                i += 1
        else:
            call = self.callsQueue[0]
            url += "/service/" + call.service + "/action/" + call.action
            params.update(call.params.get())
            files.update(call.files.get())

        signature = params.signature()
        params.put("kalsig", signature)

        self.log("request url: [%s]" % url)
        self.log("request json: [%s]" % params.toJson())

        return (url, params, files)

    @staticmethod
    def closeHandle(fh):
        fh.close()

    @staticmethod
    def openRequestUrl(url, params, files, requestHeaders):
        requestHeaders['Accept'] = 'text/xml'
        requestHeaders['Accept-encoding'] = 'gzip'
        if len(files.get()) == 0:
            requestHeaders['Content-Type'] = 'application/json'
            request = urllib2.Request(url, params.toJson(), requestHeaders)
        else:
            if 'Content-Type' in requestHeaders:
                del requestHeaders['Content-Type']
            fullParams = KalturaParams()
            fullParams.put('json', params.toJson())
            fullParams.update(files.get())
            datagen, headers = multipart_encode(fullParams.get())
            headers.update(requestHeaders)
            request = urllib2.Request(url, datagen, headers)

        try:
            f = urllib2.urlopen(request)
        except Exception, e:
            raise KalturaClientException(e, KalturaClientException.ERROR_CONNECTION_FAILED)
        return f

    @staticmethod
    def readHttpResponse(f, requestTimeout):
        if requestTimeout != None:
            readTimer = Timer(requestTimeout, KalturaClient.closeHandle, [f])
            readTimer.start()
        try:
            try:
                data = f.read()
            except AttributeError, e:      # socket was closed while reading
                raise KalturaClientException(e, KalturaClientException.ERROR_READ_TIMEOUT)
            except Exception, e:
                raise KalturaClientException(e, KalturaClientException.ERROR_READ_FAILED)
            if f.info().get('Content-Encoding') == 'gzip':
                gzipFile = gzip.GzipFile(fileobj=StringIO(data))
                try:
                    data = gzipFile.read()
                except IOError, e:
                    raise KalturaClientException(e, KalturaClientException.ERROR_READ_GZIP_FAILED)
        finally:
            if requestTimeout != None:
                readTimer.cancel()
        return data

    # Send http request
    def doHttpRequest(self, url, params = KalturaParams(), files = KalturaFiles()):
        if len(files.get()) == 0:
            requestTimeout = self.config.requestTimeout
        else:
            requestTimeout = None
            
        if requestTimeout != None:
            origSocketTimeout = socket.getdefaulttimeout()
            socket.setdefaulttimeout(requestTimeout)
        try:
            f = self.openRequestUrl(url, params, files, self.requestHeaders)
            data = self.readHttpResponse(f, requestTimeout)
            self.responseHeaders = f.info().headers
        finally:
            if requestTimeout != None:
                socket.setdefaulttimeout(origSocketTimeout)
        return data
        
    def parsePostResult(self, postResult):
        if len(postResult) > 1024:
            self.log("result (xml): %s bytes" % len(postResult))
        else:
            self.log("result (xml): %s" % postResult)

        try:        
            resultXml = minidom.parseString(postResult)
        except ExpatError, e:
            raise KalturaClientException(e, KalturaClientException.ERROR_INVALID_XML)
            
        resultNode = getChildNodeByXPath(resultXml, 'xml/result')
        if resultNode == None:
            raise KalturaClientException('Could not find result node in response xml', KalturaClientException.ERROR_RESULT_NOT_FOUND)

        execTime = getChildNodeByXPath(resultXml, 'xml/executionTime')
        if execTime != None:
            self.executionTime = getXmlNodeFloat(execTime)

        self.throwExceptionIfError(resultNode)
        return resultNode        
        
    # Call all API services that are in queue
    def doQueue(self):
        self.responseHeaders = None
        self.executionTime = None
        if len(self.callsQueue) == 0:
            self.multiRequestReturnType = None
            return None

        if self.config.format != KALTURA_SERVICE_FORMAT_XML:
            raise KalturaClientException("unsupported format: %s" % (postResult), KalturaClientException.ERROR_FORMAT_NOT_SUPPORTED)
            
        startTime = time.time()

        # get request params
        (url, params, files) = self.getRequestParams()        
            
        # reset state
        self.callsQueue = []
        
        # issue the request        
        postResult = self.doHttpRequest(url, params, files)

        endTime = time.time()
        self.log("execution time for [%s]: [%s]" % (url, endTime - startTime))

        # print server debug info to log
        serverName = None
        serverSession = None
        for curHeader in self.responseHeaders:
            if curHeader.startswith('X-Me:'):
                serverName = curHeader.split(':', 1)[1].strip()
            elif curHeader.startswith('X-Kaltura-Session:'):
                serverSession = curHeader.split(':', 1)[1].strip()
        if serverName != None or serverSession != None:
            self.log("server: [%s], session [%s]" % (serverName, serverSession))

        # parse the result            
        resultNode = self.parsePostResult(postResult)

        return resultNode
        
    def getConfig(self):
        return self.config
        
    def setConfig(self, config):
        self.config = config
        logger = self.config.getLogger()
        if isinstance(logger, IKalturaLogger):
            self.shouldLog = True
        
    def getExceptionIfError(self, resultNode):
        errorNode = getChildNodeByXPath(resultNode, 'error')
        if errorNode == None:
            return None
        messageNode = getChildNodeByXPath(errorNode, 'message')
        codeNode = getChildNodeByXPath(errorNode, 'code')
        if messageNode == None or codeNode == None:
            return None
        return KalturaException(getXmlNodeText(messageNode), getXmlNodeText(codeNode))

    # Validate the result xml node and raise exception if its an error
    def throwExceptionIfError(self, resultNode):
        exceptionObj = self.getExceptionIfError(resultNode)
        if exceptionObj == None:
            return
        raise exceptionObj

    def startMultiRequest(self):
        self.multiRequestReturnType = []
        
    def doMultiRequest(self):
        resultXml = self.doQueue()
        if resultXml == None:
            return []
        result = []
        i = 0
        for childNode in resultXml.childNodes:
            exceptionObj = self.getExceptionIfError(childNode)
            if exceptionObj != None:
                result.append(exceptionObj)
            elif getChildNodeByXPath(childNode, 'objectType') != None:
                result.append(KalturaObjectFactory.create(childNode, self.multiRequestReturnType[i]))
            elif getChildNodeByXPath(childNode, 'item/objectType') != None:
                result.append(KalturaObjectFactory.createArray(childNode, self.multiRequestReturnType[i]))
            else:
                result.append(getXmlNodeText(childNode))
            i+=1
        self.multiRequestReturnType = None
        return result

    def isMultiRequest(self):
        return (self.multiRequestReturnType != None)
        
    def getMultiRequestResult(self):
        return MultiRequestSubResult('%s:result' % len(self.callsQueue))
        
    def log(self, msg):
        if self.shouldLog:
            self.config.getLogger().log(msg)

    @staticmethod
    def generateSession(adminSecretForSigning, userId, type, partnerId, expiry = 86400, privileges = ''):
        rand = random.randint(0, 0x10000)
        expiry = int(time.time()) + expiry
        fields = [partnerId, partnerId, expiry, type, rand, userId, privileges]
        fields = map(lambda x: str(x), fields)
        info = ';'.join(fields)
        signature = KalturaClient.hash(adminSecretForSigning + info).encode('hex')
        decodedKS = signature + "|" + info
        KS = base64.b64encode(decodedKS)
        return KS

    @staticmethod
    def generateSessionV2(adminSecretForSigning, userId, type, partnerId, expiry = 86400, privileges = ''):
        # build fields array
        fields = {}
        for privilege in privileges.split(','):
            privilege = privilege.strip()
            if len(privilege) == 0:
                continue
            if privilege == '*':
                privilege = 'all:*'
            splittedPrivilege = privilege.split(':', 1)
            if len(splittedPrivilege) > 1:
                fields[splittedPrivilege[0]] = splittedPrivilege[1]
            else:
                fields[splittedPrivilege[0]] = ''

        fields[KalturaClient.FIELD_EXPIRY] = str(int(time.time()) + expiry)
        fields[KalturaClient.FIELD_TYPE] = str(type)
        fields[KalturaClient.FIELD_USER] = str(userId)

        # build fields string
        fieldsStr = urllib.urlencode(fields)
        fieldsStr = Random.get_random_bytes(KalturaClient.RANDOM_SIZE) + fieldsStr
        fieldsStr = KalturaClient.hash(fieldsStr) + fieldsStr

        # encrypt and encode
        cipher = AES.new(KalturaClient.hash(adminSecretForSigning)[:16], AES.MODE_CBC, '\0' * 16)
        if len(fieldsStr) % cipher.block_size != 0:
            fieldsStr += '\0' * (cipher.block_size - len(fieldsStr) % cipher.block_size)
        encryptedFields = cipher.encrypt(fieldsStr)
        decodedKs = "v2|%s|%s" % (partnerId, encryptedFields)
        return base64.b64encode(decodedKs).replace('+', '-').replace('/', '_')

    @staticmethod
    def hash(msg):
        m = hashlib.sha1()
        m.update(msg)
        return m.digest()

class KalturaServiceActionCall(object):
    def __init__(self, service, action, params = KalturaParams(), files = KalturaFiles()):
        self.service = service
        self.action = action
        self.params = params
        self.files = files
        
    # Return the parameters for a multi request
    def getParamsForMultiRequest(self, multiRequestIndex):
        self.params.put('service', self.service)
        self.params.put('action', self.action)
        
        multiRequestParams = KalturaParams()
        multiRequestParams.add(multiRequestIndex, self.params.get())
        return multiRequestParams.get()

    def getFilesForMultiRequest(self, multiRequestIndex):
        multiRequestParams = KalturaFiles()
        for (key, val) in self.files.get().items():
            multiRequestParams.put("%s:%s" % (multiRequestIndex, key), val)
        return multiRequestParams.get()
