from socket import *

import datetime
import operator
import sys
import select
import threading
import json
import Queue
import SocketServer


import tornado.web
import tornadio2


class IndexHandler(tornado.web.RequestHandler):
    def get(self):
    	path = 'web' + self.request.path
    	if path == 'web/':
    		path += 'index.html'
        self.render(path)
#        print "Served path[%s]" % path

class kMonitorQuery:
	SESSION_TYPE_NONE		= -1
	SESSION_TYPE_USER		= 0
	SESSION_TYPE_WIDGET		= 1
	SESSION_TYPE_ADMIN		= 2
	
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
		self.order = data['order']
			
		# Number of items
		# @var int
		self.limit = data['limit']
			
		# How many units change should trigger notification
		# @var int
		self.units = data['units']
			
		# Map of fields with single valid value
		# @var dictionary
		self.filters = data['filters']
	
	
class kMonitorQueryHandler:
	
	def __init__(self,
			name			= NotImplemented,
			query			= NotImplemented,
			connection		= NotImplemented):
			
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
			
		# The data that sent and will be sent again if changed
		# @var Thread
		self.thread = threading.Thread(target = self.digest)
		self.thread.setDaemon(True)
		self.thread.start()
		
	def stop(self):
		self.keepRunning = False
		
	def increment(self, id, count = 1):
		if id not in self.realData:
			self.realData[id] = count
		else:
			self.realData[id] += count
		
	def decrement(self, id, count = 1):
		if id in self.realData:
			self.realData[id] -= count
		
	def handle(self, apiRequest):
#		print "Digesting server[%s], address[%s], partner[%s], action[%s], cached[%s], sessionType[%s]" % (apiRequest['server'], apiRequest['address'], apiRequest['partner'], apiRequest['action'], apiRequest['cached'], apiRequest['sessionType'])
        
		now = datetime.datetime.now()
		currentSecond = now.second % 60
		
		while currentSecond != self.lastSecond:
			# move to the new second
			self.lastSecond = (self.lastSecond + 1) % 60
			
			# decrement the data from a minute ago
			if self.lastSecond in self.cachedData:
				for id in self.cachedData[self.lastSecond]:
					self.decrement(id, self.cachedData[self.lastSecond][id])
					
			self.cachedData[self.lastSecond] = {}

		
		id = apiRequest[self.query.groupBy]
		if id in self.cachedData[currentSecond]:
			self.cachedData[currentSecond][id] += 1
		else:
			self.cachedData[currentSecond][id] = 1
		
			
		self.increment(id)
		
		if (id not in self.sentData and self.realData[id] >= self.query.units) or (id in self.sentData and abs(self.realData[id] - self.sentData[id]) >= self.query.units):
			sendData = sorted(self.realData.iteritems(), key = operator.itemgetter(1), reverse = (self.query.order < 0))
			self.connection.sendCalls(self.query.name, dict(sendData[0:self.query.limit]))
			self.sentData = dict(sendData)
		
	def digest(self):
		print "Handler started [%s]" % self.name
		while self.keepRunning:
#			print "Fetch from queue"
			self.handle(self.queue.get());			
		print "Handler stopped [%s]" % self.name
		
	def ingest(self, apiRequest):
		for field in self.query.filters:
			if apiRequest[field] != self.query.filters[field]:
				return
				
#		print "Ingesting server[%s], address[%s], partner[%s], action[%s], cached[%s], sessionType[%s]" % (apiRequest['server'], apiRequest['address'], apiRequest['partner'], apiRequest['action'], apiRequest['cached'], apiRequest['sessionType'])
		self.queue.put(apiRequest)
		
monitorClients = {}
		
class kMonitorClient(tornadio2.SocketConnection):

	def on_open(self, request):
		self.handlers = {}
		monitorClients[self.session.session_id] = self

	def on_close(self):
		del monitorClients[self.session.session_id]
		for name in self.handlers:
			self.handlers[name].stop()
		    
	@tornadio2.event('applyQuery')
	def applyQuery(self, query):
		monitorQuery = kMonitorQuery(query);
		print "Queried name[%s], groupBy[%s], order[%s], limit[%s], units[%s]" % (monitorQuery.name, monitorQuery.groupBy, monitorQuery.order, monitorQuery.limit, monitorQuery.units)
		for field in monitorQuery.filters:
			print "Queried filter %s[%s]" % (field, monitorQuery.filters[field])
		
		name = monitorQuery.name
		if name in self.handlers:
			self.handlers[name].stop()
		
		# self.request is the TCP socket connected to the client
		self.handlers[name] = kMonitorQueryHandler(name = name, query = monitorQuery, connection = self);
		
	def sendCalls(self, name, calls):
		self.emit(name, calls);
		
	def handle(self, apiRequest):
		# use cloned list in order to overcome changes in the list during the iterations
		keys = self.handlers.keys()
		for name in keys:
			self.handlers[name].ingest(apiRequest)		

kMonitorRouter = tornadio2.TornadioRouter(kMonitorClient)

routes = [
   ('/', IndexHandler),
   ('/js/.*', IndexHandler) 
]
routes.extend(kMonitorRouter.urls)

application = tornado.web.Application(
    routes,
    socket_io_port = 8001)


def collectRequests():
	address = ('localhost', 6005)
	server_socket = socket(AF_INET, SOCK_DGRAM)
	server_socket.bind(address)

	while(1):
		requestData, addr = server_socket.recvfrom(2048)
		apiRequest = json.loads(requestData);
#		print "Received API request %s" % apiRequest

		# use cloned list in order to overcome changes in the list during the iterations
		keys = monitorClients.keys()
		for sessionId in keys:
			if sessionId in monitorClients:
				monitorClients[sessionId].handle(apiRequest);
	
requestsListener = threading.Thread(target = collectRequests)
requestsListener.setDaemon(True)
requestsListener.start()

kSocketServer = tornadio2.server.SocketServer(application)
