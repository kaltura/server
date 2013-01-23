import os
import re

filterMap = {}
intFilterMap = {}
inheritMap = {}

def processFile(fileName):
    global filterMap, intFilterMap, inheritMap
    mapData = None
    intMapData = None
    for curLine in file(fileName):
        # strip one line comments
        commentPos = curLine.find('//')
        if commentPos >= 0:
            curLine = curLine[:commentPos]
        
        # class decl
        matchRes = re.findall('class\s+(\w+)\s+extends\s+(\w+)', curLine)
        if len(matchRes) == 1:
            (className, baseName) = matchRes[0]
            inheritMap[className] = baseName
            filterMap[className] = []

        # external filter
        if '$map_between_objects = ' in curLine:
            mapData = ''

        if mapData != None:
            mapData += curLine
            if ';' in curLine:
                mapData = mapData[(mapData.rfind('(') + 1):mapData.rfind(')')]
                mapItems = filter(lambda x: len(x.strip()) != 0, mapData.split(','))
                mapItems = map(lambda x: x.split('=>')[-1].strip().replace('"', ''), mapItems)
                filterMap[className] = mapItems
                mapData = None

        # internal filter
        if 'kArray::makeAssociativeDefaultValue' in curLine:
            intMapData = ''

        if intMapData != None:
            intMapData += curLine
            if ';' in curLine:
                intMapData = intMapData[(intMapData.rfind('(') + 1):intMapData.rfind(')')]
                intMapData = intMapData[:intMapData[:-1].rfind(')')]
                mapItems = map(lambda x: x.strip().replace('"', '').replace("'", ''), intMapData.split(','))
                mapItems = filter(lambda y: len(y) != 0, mapItems)
                intFilterMap[className] = mapItems
                intMapData = None

def processFolder(filePath):
    for curFile in os.listdir(filePath):
        curPath = os.path.join(filePath, curFile)
        if os.path.isdir(curPath):
            processFolder(curPath)
        else:
            if os.path.splitext(curPath)[1].lower() == '.php':
                processFile(curPath)

def getFullMap(className, theFilterMap):
    result = theFilterMap[className]
    n = 0
    while inheritMap.has_key(className):
        n += 1
        className = inheritMap[className]
        if theFilterMap.has_key(className):
            result += theFilterMap[className]
    return result

baseFolder = os.path.normpath(os.path.join(os.path.dirname(__file__), '..', '..'))

for curFolder in ['alpha', 'api_v3', 'plugins']:
    processFolder(os.path.join(baseFolder, curFolder))

SPECIAL_MAPPINGS = {
    'KalturaBaseSyndicationFeedFilter':'syndicationFeedFilter',
    'KalturaBulkUploadFilter':'BatchJobLogFilter',
    'KalturaCategoryUserFilter':'categoryKuserFilter',
    'KalturaConversionProfileAssetParamsFilter':'assetParamsConversionProfileFilter',
    'KalturaConversionProfileFilter':'conversionProfile2Filter',
    'KalturaMediaEntryFilter':'entryFilter',
    'KalturaUserFilter':'kuserFilter',
}


matchedIntFilters = set([])
for curClass in filterMap:
    if not curClass.startswith('Kaltura'):
        continue
    if SPECIAL_MAPPINGS.has_key(curClass):
        intClass = SPECIAL_MAPPINGS[curClass]
    else:
        intClass = curClass[7:]
        if not intClass in intFilterMap:
            intClass = intClass[0].lower() + intClass[1:]
            if not intClass in intFilterMap:
                continue
    matchedIntFilters.add(intClass)
    extMap = getFullMap(curClass, filterMap)
    intMap = getFullMap(intClass, intFilterMap)

    missingInInt = set(extMap) - set(intMap)
    if len(missingInInt) != 0:
        print 'Error: %s is missing fields defined in %s: %s' % (intClass, curClass, ','.join(missingInInt))
    missingInExt = set(intMap) - set(extMap)
    if len(missingInExt) != 0:
        print 'Warning: %s is missing fields defined in %s: %s' % (curClass, intClass, ','.join(missingInExt))

print 'Warning: untested filters: %s' % (','.join(set(intFilterMap.keys()) - matchedIntFilters))
