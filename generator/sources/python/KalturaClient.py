from KalturaCoreClient import *
from KalturaClientBase import *
from xml.parsers.expat import ExpatError
from xml.dom import minidom
import urllib
import time
import sys
import os

from poster.streaminghttp import register_openers
from poster.encode import multipart_encode
import urllib2

# Register the streaming http handlers with urllib2
register_openers()

pluginsFolder = os.path.normpath(os.path.join(os.path.dirname(__file__), 'KalturaPlugins'))
if not pluginsFolder in sys.path:
    sys.path.append(pluginsFolder)

class MultiRequestSubResult:
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

class PluginServicesProxy:
    def addService(self, serviceName, serviceClass):
        setattr(self, serviceName, serviceClass)

class KalturaClient:
    def __init__(self, config):
        self.apiVersion = API_VERSION
        self.config = None
        self.ks = None
        self.shouldLog = False
        self.multiRequest = False
        self.callsQueue = []

        self.config = config
        logger = self.config.getLogger()
        if (logger):
            self.shouldLog = True

        self.loadPlugins()            

    def loadPlugins(self):            
        if not os.path.isdir(pluginsFolder):
            return

        pluginList = ['KalturaCoreClient']
        for fileName in os.listdir(pluginsFolder):
            (pluginClass, fileExt) = os.path.splitext(fileName)
            if fileExt.lower() != '.py':
                continue
            pluginList.append(pluginClass)

        for pluginClass in pluginList:
            self.loadPlugin(pluginClass)

    def loadPlugin(self, pluginClass):
        pluginModule = __import__(pluginClass)
        if not pluginClass in dir(pluginModule):
            return
        
        pluginClassType = getattr(pluginModule, pluginClass)

        plugin = pluginClassType.get(self)
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

        (url, params, _) = self.getRequestParams()

        # reset state
        self.callsQueue = []
        self.multiRequest = False

        result = '%s&%s' % (url, urllib.urlencode(params.get()))
        self.log("Returned url [%s]" % result)
        return result        
        
    def queueServiceActionCall(self, service, action, params = KalturaParams(), files = KalturaFiles()):
        # in start session partner id is optional (default -1). if partner id was not set, use the one in the config
        if not params.get().has_key("partnerId") or params.get()["partnerId"] == -1:
            params.put("partnerId", self.config.partnerId)
        params.addStringIfNotNone("ks", self.ks)
        call = KalturaServiceActionCall(service, action, params, files)
        self.callsQueue.append(call)

    def getRequestParams(self):
        params = KalturaParams()
        files = KalturaFiles()
        params.put("apiVersion", self.apiVersion)
        params.put("format", self.config.format)
        params.put("clientTag", self.config.clientTag)
        url = self.config.serviceUrl + "/api_v3/index.php?service="
        if self.multiRequest:
            url += "multirequest"
            i = 1
            for call in self.callsQueue:
                callParams = call.getParamsForMultiRequest(i)
                params.update(callParams)
                files.update(call.files)
                i += 1
        else:
            call = self.callsQueue[0]
            url += call.service + "&action=" + call.action
            params.update(call.params)
            files.update(call.files)

        signature = params.signature()
        params.put("kalsig", signature)

        self.log("request url: [%s]" % url)

        return (url, params, files)

    # Send http request
    def doHttpRequest(self, url, params = KalturaParams(), files = KalturaFiles()):
        if len(files.get()) == 0:
            try:
                f = urllib.urlopen(url, urllib.urlencode(params.get()))
            except Exception, e:
                raise KalturaClientException(e, KalturaClientException.ERROR_CONNECTION_FAILED)
        else:
            fullParams = params
            fullParams.update(files)
            datagen, headers = multipart_encode(fullParams.get())
            request = urllib2.Request(url, datagen, headers)
            try:
                f = urllib2.urlopen(request)
            except Exception, e:
                raise KalturaClientException(e, KalturaClientException.ERROR_CONNECTION_FAILED)

        try:
            return f.read()
        except Exception, e:
            raise KalturaClientException(e, KalturaClientException.ERROR_READ_FAILED)
        
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
        
        self.throwExceptionIfError(resultNode)

        return resultNode        
        
    # Call all API services that are in queue
    def doQueue(self):
        if len(self.callsQueue) == 0:
            self.multiRequest = False
            return None

        if self.config.format != KALTURA_SERVICE_FORMAT_XML:
            raise KalturaClientException("unsupported format: %s" % (postResult), KalturaClientException.ERROR_FORMAT_NOT_SUPPORTED)
            
        startTime = time.time()

        # get request params
        (url, params, files) = self.getRequestParams()        
            
        # reset state
        self.callsQueue = []
        self.multiRequest = False

        # issue the request        
        postResult = self.doHttpRequest(url, params, files)

        # parse the result            
        resultNode = self.parsePostResult(postResult)

        endTime = time.time()
        self.log("execution time for [%s]: [%s]" % (url, endTime - startTime))

        return resultNode

    def getKs(self):
        return self.ks
        
    def setKs(self, ks):
        self.ks = ks
        
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
        self.multiRequest = True
        
    def doMultiRequest(self):
        resultXml = self.doQueue()
        result = []
        for childNode in resultXml.childNodes:
            exceptionObj = self.getExceptionIfError(childNode)
            if exceptionObj != None:
                result.append(exceptionObj)
            elif getChildNodeByXPath(childNode, 'objectType') != None:
                result.append(KalturaObjectFactory.create(childNode, KalturaObjectBase))
            else:
                result.append(getXmlNodeText(childNode))
        return result

    def isMultiRequest(self):
        return self.multiRequest
        
    def getMultiRequestResult(self):
        return MultiRequestSubResult('%s:result' % len(self.callsQueue))
        
    def log(self, msg):
        if self.shouldLog:
            self.config.getLogger().log(msg)

class KalturaServiceActionCall:
    def __init__(self, service, action, params = KalturaParams(), files = KalturaFiles()):
        self.service = service
        self.action = action
        self.params = params
        self.files = files
        
    # Return the parameters for a multi request
    def getParamsForMultiRequest(self, multiRequestIndex):
        multiRequestParams = KalturaParams()
        multiRequestParams.put("%s:service" % multiRequestIndex, self.service)
        multiRequestParams.put("%s:action" % multiRequestIndex, self.action)
        for (key, val) in self.params.get().items():
            multiRequestParams.put("%s:%s" % (multiRequestIndex, key), val)
        return multiRequestParams
