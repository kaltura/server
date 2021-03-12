from socket import *

import time
import operator
import sys
import select
import threading
import json
import Queue
import SocketServer
import tornado.web
import tornadio2
import gc


class IndexHandler(tornado.web.RequestHandler):
    def get(self):
    	path = 'web' + self.request.path
    	if path == 'web/' or path.find('..') >= 0:
    		path = 'web/index.html'
    		
        self.render(path)
#        print "Served path[%s]" % path

class kMonitorQuery:
	def __init__(self, data):
			
		# Unique identifier per TCP client
		# @var string
		self.name = data['name']
			
		# Single field to group by
		# server / address / partner / action / type
		# @var string
		self.groupBy = data['groupBy']
			
		# Ascending (1) or descending (-1)
		# @var int
		self.order = int(data['order'])
			
		# Number of items
		# @var int
		self.limit = int(data['limit'])
			
		# How many units change should trigger notification
		# @var int
		self.units = int(data['units'])
			
		# Map of fields with single valid value
		# @var dictionary
		self.filters = data['filters']
	
	
class kMonitorQueryHandler(threading.Thread):
	
	def __init__(self,
			name			= NotImplemented,
			query			= NotImplemented,
			connection		= NotImplemented):
			
		threading.Thread.__init__(self, target = self.digest)
		self.setDaemon(True)
		
		# The last second that handled
		# @var int
		self.lastSecond = -1
		
		# Unique identifier per TCP client
		# @var string
		self.name = name
			
		# Full query as received from the TCP client
		# @var kMonitorQuery
		self.query = query
			
		# Reference to the client socket
		# @var kMonitorClient
		self.connection = connection
			
		# Queue of filtered requests
		# @var Queue
		self.queue = Queue.Queue()
			
		# Counters grouped by time % 60 (current second), later by values
		# @var dictionary[0-60][value] = count
		self.cachedData = {}
			
		# All values grouped according to the query group by
		# @var dictionary
		self.realData = {}
			
		# The data that sent and will be sent again if changed
		# @var dictionary
		self.sentData = {}
			
		# Indicates that the thread should keep running
		# @var boolean
		self.keepRunning = True
			
		# Number of handled requests since last status
		# @var int
		self.handledRequests = 0
		
	def stop(self):
		self.keepRunning = False
		
	def increment(self, id, count = 1):
		self.realData.setdefault(id, 0)
		self.realData[id] += count
		
	def decrement(self, id, count = 1):
		if id in self.realData:
			self.realData[id] -= count
		if self.realData[id] <= 0:
			del self.realData[id]
		
	def handle(self, apiRequest):
#		print "Digesting server[%s], address[%s], partner[%s], action[%s], cached[%s], sessionType[%s]" % (apiRequest['server'], apiRequest['address'], apiRequest['partner'], apiRequest['action'], apiRequest['cached'], apiRequest['sessionType'])
        
		self.handledRequests += 1
        
		now = int(time.time())
		currentSecond = now % 60
		
		while currentSecond != self.lastSecond:
			# move to the new second
			self.lastSecond = (self.lastSecond + 1) % 60
			
			# decrement the data from a minute ago
			if self.lastSecond in self.cachedData:
				for id in self.cachedData[self.lastSecond]:
					self.decrement(id, self.cachedData[self.lastSecond][id])
					
			self.cachedData[self.lastSecond] = {}

		
		id = apiRequest[self.query.groupBy]
		self.cachedData[currentSecond].setdefault(id, 0)
		self.cachedData[currentSecond][id] += 1		
			
		self.increment(id)
		
		if (id not in self.sentData and self.realData[id] >= self.query.units) or (id in self.sentData and abs(self.realData[id] - self.sentData[id]) >= self.query.units):
			sendData = sorted(self.realData.iteritems(), key = operator.itemgetter(1), reverse = (self.query.order < 0))
			self.connection.sendCalls(self.query.name, dict(sendData[:self.query.limit]))
			self.sentData = dict(sendData)
		
	def digest(self):
		print "Handler started [%s]" % self.name
		
		try:
			while self.keepRunning and not self.connection.is_closed:
	#			print "Fetch from queue"
				self.handle(self.queue.get());		
		finally:
			print "Handler stopped [%s]" % self.name
		
	def ingest(self, apiRequest):
		for field in self.query.filters:
			if apiRequest[field] != self.query.filters[field]:
				return
				
#		print "Ingesting server[%s], address[%s], partner[%s], action[%s], cached[%s], sessionType[%s]" % (apiRequest['server'], apiRequest['address'], apiRequest['partner'], apiRequest['action'], apiRequest['cached'], apiRequest['sessionType'])
		self.queue.put(apiRequest)
		
	def getStatus(self):
		ret = self.name + " q[" + str(self.queue.qsize()) + "] r[" + str(self.handledRequests) + "]"
		self.handledRequests = 0
		return ret
				
				
class kMonitorClient(tornadio2.SocketConnection):

	def on_open(self, request):
		print "Client connected [%s]" % (self.session.session_id)
		self.handlers = {}
		self.handlersLock = threading.Lock()
		
		monitorClientsLock.acquire()
		try:
			monitorClients[self.session.session_id] = self
		finally:
			monitorClientsLock.release()

	def on_close(self):
		
		monitorClientsLock.acquire()
		try:
			del monitorClients[self.session.session_id]
		finally:
			monitorClientsLock.release()
			
		self.handlersLock.acquire()
		try:
			for name in self.handlers:
				self.handlers[name].stop()
		finally:
			self.handlersLock.release()
			
		print "Client disconnected [%s]" % (self.session.session_id)
		    
	@tornadio2.event('applyQuery')
	def applyQuery(self, query):
		monitorQuery = kMonitorQuery(query);
		print "Queried name[%s], groupBy[%s], order[%s], limit[%s], units[%s]" % (monitorQuery.name, monitorQuery.groupBy, monitorQuery.order, monitorQuery.limit, monitorQuery.units)
		for field in monitorQuery.filters:
			print "Queried filter %s[%s]" % (field, monitorQuery.filters[field])
		
		name = monitorQuery.name
		self.handlersLock.acquire()
		try:
			if name in self.handlers:
				self.handlers[name].stop()
			# self.request is the TCP socket connected to the client
			self.handlers[name] = kMonitorQueryHandler(name = name, query = monitorQuery, connection = self);
			self.handlers[name].start();
		finally:
			self.handlersLock.release()
		
	def sendCalls(self, name, calls):
		try:
			self.emit(name, calls);
		except IndexError:
			print "Failed sending calls"
		
	def handle(self, apiRequest):
		# use cloned list in order to overcome changes in the list during the iterations
		self.handlersLock.acquire()
		try:
			for name in self.handlers:
				self.handlers[name].ingest(apiRequest)		
		finally:
			self.handlersLock.release()

	def printStatus(self):
		status = ""
		for name in self.handlers:
			status += self.handlers[name].getStatus() + ", "		
		print "Client [%s] %s" % (self.session.session_id, status)
		
class kMonitorStatus(threading.Thread):
	
	def __init__(self, interval):
		self.interval = interval
		threading.Thread.__init__(self, target = self.printStatus)
		self.setDaemon(True)
		
	def printStatus(self):
		print "Status reported every %d seconds" % (self.interval)
		while(True):
			time.sleep(self.interval)
			for sessionId in monitorClients:
				monitorClients[sessionId].printStatus();
			print "Status reported"
			gc.collect()

def collectRequests():
	address = ('', 6005)
	server_socket = socket(AF_INET, SOCK_DGRAM)
	server_socket.bind(address)

	while(1):
		requestData, addr = server_socket.recvfrom(2048)
				
		try:
			apiRequest = json.loads(requestData);
		except ValueError:
			continue

		#print "Received API request %s" % apiRequest
		
		# use cloned list in order to overcome changes in the list during the iterations
		monitorClientsLock.acquire()
		try:
			for sessionId in monitorClients:
				monitorClients[sessionId].handle(apiRequest);
		finally:
			monitorClientsLock.release()
	
	
monitorClients = {}
monitorClientsLock = threading.Lock()
		
monitorStatus = kMonitorStatus(60)
monitorStatus.start();

kMonitorRouter = tornadio2.TornadioRouter(kMonitorClient)

routes = [
   ('/', IndexHandler),
   ('/js/.*', IndexHandler) 
]
routes.extend(kMonitorRouter.urls)

application = tornado.web.Application(
    routes,
    socket_io_port = 8001)

requestsListener = threading.Thread(target = collectRequests)
requestsListener.setDaemon(True)
requestsListener.start()

kSocketServer = tornadio2.server.SocketServer(application)	
		
