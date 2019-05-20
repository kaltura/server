from optparse import OptionParser
from threading import Thread
from gzip import GzipFile
from math import isnan
import SocketServer
import operator
import socket
import time
import json
import sys

eventsBuffer = {}

def stripNewlines(value):
	return value.replace('\n', ' ').replace('\r', ' ')

def parseAddress(addressStr):
    address, port = addressStr.split(':')
    return (address, int(port))

class ReaderThread(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        self.sock.bind(parseAddress(options.udpAddress))
        self.outputFile = None

    def run(self):
        global eventsBuffer
        
        curSlot = []
        lastSlotIndex = 0
        while True:
            data, addr = self.sock.recvfrom(4096)
            #print data
            curTime = int(time.time())
            curSlotIndex = curTime % options.window
            if curSlotIndex != lastSlotIndex:
                eventsBuffer[lastSlotIndex] = curSlot
                curSlot = []
                lastSlotIndex = curSlotIndex
            for curMessage in data.split('\0'):
                try:
                    curSlot.append(json.loads(curMessage))
                except UnicodeDecodeError:
                    pass
                except ValueError:
                    pass
            if not options.saveInput:
                continue
            if (self.outputFile == None or
                curTime / options.saveWindow != self.fileOpenWindow):
                if self.outputFile != None:
                    self.outputFile.close()
                self.fileOpenWindow = curTime / options.saveWindow
                outputFilename = time.strftime(options.outputFileFormat, time.localtime(self.fileOpenWindow * options.saveWindow))
                self.outputFile = GzipFile(outputFilename, 'a')
            self.outputFile.write(curMessage.replace('\0', '\n') + '\n')

def safeFloat(num):
    try:
        return float(num)
    except ValueError:
        return float('nan')
                
class CommandHandler(SocketServer.BaseRequestHandler):
    AGGREGATED_FIELDS = 'xn'
    
    @staticmethod
    def getRowBuilder(groupColumns, selectColumns):
        def result(obj):
            # get groupBy key
            groupValues = []
            for column in groupColumns:
                if obj.has_key(column):
                    groupValues.append(str(obj[column]))
                else:
                    groupValues.append('Missing')
            # get select values
            selectValues = []
            for column in selectColumns:
                if obj.has_key(column):
                    selectValues.append(str(obj[column]))
                else:
                    selectValues.append('Missing')
            # get aggregated fields
            aggregatedFields = []
            for fieldName in CommandHandler.AGGREGATED_FIELDS:
                value = float('nan')
                if obj.has_key(fieldName):
                    value = obj[fieldName]
                if type(value) == str:
                    if '.' in value:
                        value = float(value)
                    else:
                        value = int(value)
                aggregatedFields.append(value)
            return ('\t'.join(groupValues), '\t'.join(selectValues), tuple(aggregatedFields))
        return result

    @staticmethod
    def dictIncrement(theDict, (groupByValues, selectValues, aggregatedFields)):
        executionTime = float(aggregatedFields[0])
        theDict.setdefault(groupByValues, [selectValues, executionTime, 0, (0,) * len(CommandHandler.AGGREGATED_FIELDS)])      # maxSelect, maxTime, totalCount, aggregatedFields
        row = theDict[groupByValues]
        row[2] += 1
        row[3] = tuple(map(sum, zip(row[3], map(float, aggregatedFields))))

        if not isnan(executionTime) and (isnan(theDict[groupByValues][1]) or theDict[groupByValues][1] < executionTime):
            theDict[groupByValues][0] = selectValues
            theDict[groupByValues][1] = executionTime
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
                elif operator == '>':
                    refValue = safeFloat(refValue)
                    fieldValue = safeFloat(fieldValue)
                    operatorResult = (not isnan(refValue) and not isnan(fieldValue) and fieldValue > refValue)
                elif operator == '<':
                    refValue = safeFloat(refValue)
                    fieldValue = safeFloat(fieldValue)
                    operatorResult = (not isnan(refValue) and not isnan(fieldValue) and fieldValue < refValue)
                if negated:
                    operatorResult = not operatorResult
                if not operatorResult:
                    return False
            return True
        return result

    def handle(self):
        global eventsBuffer
        
        command = self.request.recv(4096).strip()
        cmdFilter, cmdGroupBy, cmdSelect = command.split('/')

        # init filter
        filterFunction = self.getFilterFunction(cmdFilter)

        # init group by
        cmdGroupBy = filter(lambda x: x not in CommandHandler.AGGREGATED_FIELDS, cmdGroupBy).strip()
        getGroupByColumns = self.getRowBuilder(cmdGroupBy, cmdSelect)

        # process the events
        result = {}
        for i in xrange(options.window):
            if not eventsBuffer.has_key(i):
                continue
            filteredSlot = filter(filterFunction, eventsBuffer[i])
            curSlot = map(getGroupByColumns, filteredSlot)
            result = reduce(self.dictIncrement, curSlot, result)

        # format the result
        aggrFieldsFormat = ''
        aggrFieldsIndexes = []
        for fieldIndex in xrange(len(CommandHandler.AGGREGATED_FIELDS)):
            fieldFormat = ''
            for _, _, _, aggrFields in result.values():
                fieldValue = aggrFields[fieldIndex]
                if isnan(fieldValue):
                    continue                
                if type(fieldValue) == float:
                    fieldFormat = '%.3f\t'
                    break
                fieldFormat = '%d\t'
            if fieldFormat == '':
                continue
            aggrFieldsFormat += fieldFormat
            aggrFieldsIndexes.append(fieldIndex)

        resultText = ''
        if len(aggrFieldsFormat) > 0:
            aggrFieldGetter = operator.itemgetter(*aggrFieldsIndexes)
            for groupByValues, (selectValues, _, count, aggrFields) in result.items():
                resultText += ('%s\t' % count + 
                    aggrFieldsFormat % aggrFieldGetter(aggrFields) +
                    '%s\t%s\n' % (stripNewlines(groupByValues), stripNewlines(selectValues)))
        else:
            for groupByValues, (selectValues, _, count, _) in result.items():
                resultText += '%s\t%s\t%s\n' % (count, stripNewlines(groupByValues), stripNewlines(selectValues))
        self.request.sendall(resultText)
        
class CommandThread(Thread):
    def run(self):
        SocketServer.TCPServer.allow_reuse_address = True
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
    parser.add_option("-s", "--save-input", dest="saveInput",action="store_true",
                      help="save the raw input to a file")
    parser.add_option("-W", "--save-window", dest="saveWindow",default=3600,type="int",
                      help="determines the interval in seconds for reopening the output file", metavar="SECS")
    parser.add_option("-f", "--output-format", dest="outputFileFormat",default='/var/log/apimon/apimon-%Y-%m-%d-%H.log.gz',
                      help="sets the output file naming format", metavar="FMT")
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
