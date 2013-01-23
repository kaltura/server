from xml.dom import minidom
import sys

def getNodeName(xml):
    return '%s#%s' % (xml.nodeName, xml.getAttribute('name'))

def getChildElements(xml):
    return filter(lambda x: isinstance(x, minidom.Element), xml.childNodes)

def getChildByName(xml, nodeName):
    for node in getChildElements(xml):
        if getNodeName(node) == nodeName:
            return node
    return None

def getTail(string, prefix):
    if not string.startswith(prefix):
        return None
    return string[len(prefix):]

def addDiff(diffType, elementType, elementName):
    global diffs
    diffs.append((diffType, elementType, elementName))

def parseDiff(diffType, path, curDiff):
    path = '%s/%s' % (path, curDiff)
    match = getTail(path, '//xml#/services#/service#')
    if match != None:
        if match.find('/action#') >= 0:
            match = match.replace('/action#', '.')
            if match.find('/param#') >= 0:
                addDiff(diffType, 'parameter', match.replace('/param#', '.'))
            else:
                addDiff(diffType, 'action', match)
        else:
            addDiff(diffType, 'service', match)
        return
    
    match = getTail(path, '//xml#/plugins#/plugin#')
    if match != None:
        addDiff(diffType, 'plugin', match)
        return

    match = getTail(path, '//xml#/enums#/enum#')
    if match != None:
        if match.find('/const#') >= 0:
            addDiff(diffType, 'enum value', match.replace('/const#', '.'))
        else:
            addDiff(diffType, 'enum', match)
        return

    match = getTail(path, '//xml#/classes#/class#')
    if match != None:
        if match.find('/property#') >= 0:
            addDiff(diffType, 'property', match.replace('/property#', '.'))
        else:
            addDiff(diffType, 'object', match)
        return

    print 'Failed to parse %s' % path            

def xmlDiff(path, oldXml, newXml):
    oldXmlNodeNames = set(map(lambda x: getNodeName(x), getChildElements(oldXml)))
    newXmlNodeNames = set(map(lambda x: getNodeName(x), getChildElements(newXml)))

    for curDiff in oldXmlNodeNames.difference(newXmlNodeNames):
        parseDiff('removed', path, curDiff)
    for curDiff in newXmlNodeNames.difference(oldXmlNodeNames):
        parseDiff('added', path, curDiff)

    for curMatch in oldXmlNodeNames.intersection(newXmlNodeNames):
        xmlDiff(
            '%s/%s' % (path, curMatch),
            getChildByName(oldXml, curMatch),
            getChildByName(newXml, curMatch))

def EditingDistance(str1, str2):
    if str1 == str2:
        return 0
    
    d = [[0 for col in range(len(str2) + 1)] for row in range(len(str1) + 1)]
    
    for i in range(len(str1) + 1):
        d[i][0] = i
    for j in range(len(str2) + 1):
        d[0][j] = j
        
    for i in range(1, len(str1) + 1):
        for j in range(1, len(str2) + 1):
            if str1[i - 1] == str2[j - 1]:
                cost = 0
            else:
                cost = 2
            d[i][j] = min(
                d[i - 1][j] + 1,        # deletion
                d[i][j - 1] + 1,        # insertion
                d[i - 1][j - 1] + cost  # substitution
                )
    return d[len(str1)][len(str2)]

def getDiffDistance(diff1, diff2):
    elem1 = diff1[2]
    elem2 = diff2[2]
    elem1 = elem1.split('.')[-1]
    elem2 = elem2.split('.')[-1]
    return EditingDistance(elem1, elem2)

def printDiff(curDiff):
    print ' '.join(curDiff)

# parse command line
try:
    (_, newXmlFileName, oldXmlFileName) = sys.argv
except ValueError:
    print 'Usage:\n\t%s <new schema xml> <old schema xml>' % sys.argv[0]
    sys.exit(1)

# perform diff between the two schemas
newXml = minidom.parse(newXmlFileName)
oldXml = minidom.parse(oldXmlFileName)

diffs = []
xmlDiff('/', oldXml, newXml)

# start with shortest diff
minLength = None
for curIndex in xrange(len(diffs)):
    (diffType, elementType, elementName) = diffs[curIndex]
    if minLength == None or len(elementName) < minLength:
        minLength = len(elementName)
        diffIndex = curIndex

while True:
    # extract & print the diff
    curDiff = diffs[diffIndex]
    printDiff(curDiff)
    diffs = diffs[:diffIndex] + diffs[(diffIndex + 1):]

    if len(diffs) == 0:
        break

    # select the closest diff to one just printed
    minDist = None
    for curIndex in xrange(len(diffs)):
        curDist = (getDiffDistance(curDiff, diffs[curIndex]), len(diffs[curIndex]))
        if minDist == None or curDist < minDist:
            minDist = curDist
            diffIndex = curIndex
