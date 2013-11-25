from optparse import OptionParser
from threading import Thread
from math import isnan
import SocketServer
import socket
import time
import json
import sys

eventsBuffer = {}

def parseAddress(addressStr):
    address, port = addressStr.split(':')
    return (address, int(port))

class ReaderThread(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        self.sock.bind(parseAddress(options.udpAddress))

    def run(self):
        global eventsBuffer
        
        curSlot = []
        lastSlotIndex = 0
        while True:
            data, addr = self.sock.recvfrom(4096)
            #print data
            curSlotIndex = int(time.time()) % options.window
            if curSlotIndex != lastSlotIndex:
                eventsBuffer[lastSlotIndex] = curSlot
                curSlot = []
                lastSlotIndex = curSlotIndex
            for curMessage in data.split('\0'):
                curSlot.append(json.loads(curMessage))

class CommandHandler(SocketServer.BaseRequestHandler):
    FIELD_EXECUTION_TIME = 'x'
    
    @staticmethod
    def getRowBuilder(columns):
        def result(obj):
            # get groupBy key
            values = []
            for column in columns:
                if obj.has_key(column):
                    values.append(str(obj[column]))
                else:
                    values.append('Missing')
            # get execution time
            executionTime = float('nan')
            if obj.has_key(CommandHandler.FIELD_EXECUTION_TIME):
                executionTime = float(obj[CommandHandler.FIELD_EXECUTION_TIME])
            return ('\t'.join(values), executionTime)
        return result

    @staticmethod
    def dictIncrement(theDict, (theKey, executionTime)):
        theDict.setdefault(theKey, [0, 0])
        theDict[theKey][0] += 1
        theDict[theKey][1] += executionTime
        return theDict

    @staticmethod
    def getFilterFunction(filtersDef):
        filters = []
        for curFilterDef in filtersDef.split(','):
            curFilterDef = curFilterDef.strip()
            if len(curFilterDef) < 3:
                continue            
            field = curFilterDef[0]
            if curFilterDef[1] == '!':
                negated = True
                operator = curFilterDef[2]
                refValue = curFilterDef[3:]
            else:
                negated = False
                operator = curFilterDef[1]
                refValue = curFilterDef[2:]
            filters.append((negated, field, operator, refValue.lower()))
                
        def result(obj):
            for (negated, field, operator, refValue) in filters:
                if not obj.has_key(field):
                    if not negated:
                        return False
                    continue
                fieldValue = str(obj[field]).lower()
                operatorResult = False
                if operator == '=':
                    operatorResult = (refValue == fieldValue)
                elif operator == '~':
                    operatorResult = (refValue in fieldValue)
                if negated:
                    operatorResult = not operatorResult
                if not operatorResult:
                    return False
            return True
        return result

    def handle(self):
        global eventsBuffer
        
        command = self.request.recv(4096).strip()
        cmdFilter, cmdGroupBy = command.split('/')

        # init filter
        filterFunction = self.getFilterFunction(cmdFilter)

        # init group by
        cmdGroupBy = cmdGroupBy.replace(CommandHandler.FIELD_EXECUTION_TIME, '').strip()
        getGroupByColumns = self.getRowBuilder(cmdGroupBy)

        # process the events
        result = {}
        for i in xrange(options.window):
            if not eventsBuffer.has_key(i):
                continue
            filteredSlot = filter(filterFunction, eventsBuffer[i])
            curSlot = map(getGroupByColumns, filteredSlot)
            result = reduce(self.dictIncrement, curSlot, result)

        # format the result
        hasExecTime = False
        for _, execTime in result.values():
            if not isnan(execTime):
                hasExecTime = True
                break

        resultText = ''
        if hasExecTime:
            for key, (count, execTime) in result.items():
                resultText += '%s\t%.3f\t%s\n' % (count, execTime, key)
        else:
            for key, (count, _) in result.items():
                resultText += '%s\t%s\n' % (count, key)
        self.request.sendall(resultText)
        
class CommandThread(Thread):
    def run(self):
        server = SocketServer.TCPServer(parseAddress(options.tcpAddress), CommandHandler)
        server.serve_forever()

if __name__ == '__main__':
    # parse the command line
    parser = OptionParser()
    parser.add_option("-w", "--window", dest="window",default=10,type="int",
                      help="the aggregation window size in seconds", metavar="SECS")
    parser.add_option("-t", "--tcp-address", dest="tcpAddress",default="127.0.0.1:6005",
                      help="the TCP address to listen on", metavar="ADDR")
    parser.add_option("-u", "--udp-address", dest="udpAddress",default=":6005",
                      help="the UDP address to listen on", metavar="ADDR")
    (options, args) = parser.parse_args()

    # start the worker threads
    rt = ReaderThread()
    ct = CommandThread()
    rt.start()
    ct.start()

    # sleep forever
    print '%s started' % (time.ctime())
    try:
        time.sleep(options.window)
        print '%s warmed up' % (time.ctime())
        while True:
            time.sleep(86400)
    except KeyboardInterrupt:
        pass
    print '%s quitting' % (time.ctime())
    sys.exit(1)
