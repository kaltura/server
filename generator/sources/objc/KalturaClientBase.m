#import <CommonCrypto/CommonDigest.h>
#import "KalturaClientBase.h"
#import "ASIFormDataRequest.h"

static NSString* KALTURA_SERVICE_BASE_URL = @"/api_v3/index.php";
static NSString* KALTURA_SERVICE_FORMAT_XML = @"2";

/*
 Class KalturaSimpleTypeParser
 */
@implementation KalturaSimpleTypeParser

+ (BOOL)parseBool:(NSString*)aStr
{
    if (aStr == nil)
        return NO;
    if ([aStr compare:@"1"] == NSOrderedSame)
        return NO;
    return YES;
}

+ (int)parseInt:(NSString*)aStr
{
    if (aStr == nil)
        return 0;
    return [aStr intValue];
}

+ (double)parseFloat:(NSString*)aStr
{
    if (aStr == nil)
        return 0.0;
    return [aStr doubleValue];

}

@end

/*
 Class KalturaObjectBase
 */
@implementation KalturaObjectBase

- (void)setObjectProperty:(NSString*)aPropNameCap withValue:(id)aValue isSimple:(BOOL)aIsSimple
{
    NSString* postFix = @"";
    if (aIsSimple)
        postFix = @"FromString";
    NSMutableString* selName = [[NSMutableString alloc] initWithFormat:@"set%@%@:", aPropNameCap, postFix];
    SEL sel = NSSelectorFromString(selName);
    if (![self respondsToSelector:sel])
        @throw @"Unexpected: object does not respond to setter";            // shoundn't happen since the property was already validated by getTypeOfProperty
    [self performSelector:sel withObject:aValue];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
}

@end

/*
 Class KalturaException
 */
@implementation KalturaException : KalturaObjectBase

@synthesize code = _code;
@synthesize message = _message;

- (KalturaFieldType)getTypeOfCode
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMessage
{
    return KFT_String;
}

@end

/*
 Class KalturaObjectFactory
 */
@implementation KalturaObjectFactory

+ (KalturaObjectBase*)createByName:(NSString*)aName
{
    Class objClass = NSClassFromString(aName);
    if (objClass == nil)
    {
        return nil;
    }
    if (![objClass isSubclassOfClass:[KalturaObjectBase class]])
    {
        return nil;
    }
    return [[objClass alloc] init];
}

@end

///////////////////////// Request Building /////////////////////////

/*
 Class KalturaParam
 */
@interface KalturaParam : NSObject

@property (nonatomic, copy) NSString* key;
@property (nonatomic, copy) NSString* value;

- (id)initWithKey:(NSString*)aKey withValue:(NSString*)aValue;

@end

@implementation KalturaParam

@synthesize key = _key;
@synthesize value = _value;

- initWithKey:(NSString*)aKey withValue:(NSString*)aValue
{
    self = [super init];
    if (self)
    {
        self.key = aKey;
        self.value = aValue;
    }
    return self;
}

- (NSComparisonResult)compare:(KalturaParam*)aOtherParam
{
    NSComparisonResult keyCompare = [self.key compare:aOtherParam.key];
    if (keyCompare != NSOrderedSame)
        return keyCompare;
    
    return [self.value compare:aOtherParam.value];
}

@end

/*
 Class KalturaFile
 */
@interface KalturaFile : NSObject

@property (nonatomic, copy) NSString* key;
@property (nonatomic, copy) NSString* value;

- (id)initWithKey:(NSString*)aKey withFileName:(NSString*)aValue;

@end

@implementation KalturaFile

@synthesize key = _key;
@synthesize value = _value;

- initWithKey:(NSString*)aKey withFileName:(NSString*)aValue
{
    self = [super init];
    if (self)
    {
        self.key = aKey;
        self.value = aValue;
    }
    return self;
}

@end

/*
 Class KalturaParams
 */
@implementation KalturaParams

- (id)init
{
    self = [super init];
    if (self) {
        self->_params = [[NSMutableArray alloc] init];
        self->_files = [[NSMutableArray alloc] init];
        self->_prefix = [[NSMutableString alloc] init];
    }
    
    return self;
}

- (void)dealloc {
    [self->_prefix release];
    [self->_files release];
    [self->_params release];
    [super dealloc];
}

- (void)pushKey:(NSString*)aKey
{    
    [self->_prefix appendString:aKey];
    [self->_prefix appendString:@":"];
}

- (void)popKey:(NSString*)aKey
{
    int curLength = self->_prefix.length;
    NSRange range = NSMakeRange(curLength - aKey.length - 1, curLength);
    [self->_prefix deleteCharactersInRange:range];
}

- (void)setPrefix:(NSString*)aPrefix
{
    [self->_prefix setString:aPrefix];
}

- (NSString*)get:(NSString*)aKey
{
    for (KalturaParam* param in self->_params)
    {
        if ([aKey compare:param.key] == NSOrderedSame)
        {
            return param.value;
        }
    }
    return nil;
}

- (void)putKey:(NSString*)aKey withString:(NSString*)aVal
{
    if (self->_prefix.length != 0)
    {
        aKey = [NSString stringWithFormat:@"%@%@", self->_prefix, aKey];
    }
    KalturaParam* param = [[KalturaParam alloc] initWithKey:aKey withValue:aVal];
    [self->_params addObject:param];
}

- (void)putNullKey:(NSString*)aKey
{
    NSString* nullKey = [NSString stringWithFormat:@"%@%@", aKey, @"__null"];
    [self putKey:nullKey withString:@""];
}

- (void)addIfDefinedKey:(NSString*)aKey withFileName:(NSString*)aFileName;
{
    if (aFileName == nil)
        return;
    
    if (self->_prefix.length != 0)
    {
        aKey = [NSString stringWithFormat:@"%@%@", self->_prefix, aKey];
    }
    KalturaFile* param = [[KalturaFile alloc] initWithKey:aKey withFileName:aFileName];
    [self->_files addObject:param];    
}

- (void)addIfDefinedKey:(NSString*)aKey withBool:(BOOL)aVal
{
    if (aVal == KALTURA_UNDEF_BOOL)
        return;
    
    if (aVal == KALTURA_NULL_BOOL)
    {
        [self putNullKey:aKey];
    }
    else if (aVal)
    {
        [self putKey:aKey withString:@"1"];
    }
    else
    {
        [self putKey:aKey withString:@"0"];
    }
}

- (void)addIfDefinedKey:(NSString*)aKey withInt:(int)aVal
{
    if (aVal == KALTURA_UNDEF_INT)
        return;
    
    if (aVal == KALTURA_NULL_INT)
    {
        [self putNullKey:aKey];
    }
    else
    {
        [self putKey:aKey withString:[NSString stringWithFormat:@"%d", aVal]];
    }
}

- (void)addIfDefinedKey:(NSString*)aKey withFloat:(double)aVal
{
    if (isnan(aVal))        // cannot compare to KALTURA_UNDEF_FLOAT since NaN != NaN
        return;

    if (aVal == KALTURA_NULL_FLOAT)
    {
        [self putNullKey:aKey];
    }
    else
    {
        [self putKey:aKey withString:[NSString stringWithFormat:@"%f", aVal]];
    }
}

- (void)addIfDefinedKey:(NSString*)aKey withString:(NSString*)aVal
{
    if (aVal == KALTURA_UNDEF_STRING)
        return;
    
    if ([aVal compare:KALTURA_NULL_STRING] == NSOrderedSame)
    {
        [self putNullKey:aKey];
    }
    else
    {
        [self putKey:aKey withString:aVal];
    }
}

- (void)addIfDefinedKey:(NSString*)aKey withObject:(KalturaObjectBase*)aVal
{
    if (aVal == nil)
        return;
    
    [self pushKey:aKey]; 

    [aVal toParams:self isSuper:NO];
    
    [self popKey:aKey]; 
}

- (void)addIfDefinedKey:(NSString*)aKey withArray:(NSArray*)aVal
{
    if (aVal == nil)
        return;

    [self pushKey:aKey]; 
    
    if (aVal.count == 0)
    {
        [self putKey:@"-" withString:@""];
    }
    else
    {
        for (int index = 0; index < aVal.count; index++)
        {            
            [self addIfDefinedKey:[NSString stringWithFormat:@"%d", index] 
                       withObject:[aVal objectAtIndex:index]];
        }
    }
    
    [self popKey:aKey]; 
}

- (void)sign
{
    [self->_params sortUsingSelector:@selector(compare:)];
    
    unsigned char md5Hash[CC_MD5_DIGEST_LENGTH];
    CC_MD5_CTX md5Ctx;

    CC_MD5_Init(&md5Ctx);
    for (KalturaParam* curParam in self->_params)
    {
        const char* keyPtr = [curParam.key UTF8String];
        const char* valuePtr = [curParam.value UTF8String];
        CC_MD5_Update(&md5Ctx, keyPtr, strlen(keyPtr));
        CC_MD5_Update(&md5Ctx, valuePtr, strlen(valuePtr));
    }
    CC_MD5_Final(md5Hash, &md5Ctx);

    NSMutableString* kalSig = [NSMutableString stringWithCapacity:(CC_MD5_DIGEST_LENGTH * 2)];
    for (int i = 0; i < CC_MD5_DIGEST_LENGTH; i++)
        [kalSig appendFormat:@"%02x", md5Hash[i]];
    
    [self putKey:@"kalsig" withString:kalSig];
}

- (void)addToRequest:(ASIFormDataRequest*)aRequest
{
    while (self->_params.count)
    {
        KalturaParam* curParam = [self->_params lastObject];
        [aRequest addPostValue:curParam.value forKey:curParam.key];
        [self->_params removeLastObject];
    }
           
    while (self->_files.count)
    {
        KalturaFile* curFile = [self->_files lastObject];
        [aRequest addFile:curFile.value forKey:curFile.key];
        [self->_files removeLastObject];
    }
}

- (NSString*)getQueryString
{
    NSMutableString* result = [[NSMutableString alloc] init];
    while (self->_params.count)
    {
        KalturaParam* curParam = [self->_params lastObject];
        NSString *encodedVal = (NSString*)CFURLCreateStringByAddingPercentEscapes(
            NULL, 
            (CFStringRef)curParam.value, 
            NULL, 
            (CFStringRef)@"!*'();:@&=+$,/?%#[] \"\\<>{}|^~`", 
            kCFStringEncodingUTF8);
        [result appendFormat:@"%@=%@&", curParam.key, encodedVal];
        [self->_params removeLastObject];
    }
    return result;
}

@end

///////////////////////// Response Parsing /////////////////////////

/*
 Class KalturaXmlParserBase
 */
@implementation KalturaXmlParserBase

@synthesize parser = _parser;
@synthesize delegate = _delegate;

- (void)dealloc 
{
    [self detach];
    [super dealloc];
}

- (void)attachToParser:(NSXMLParser*)aParser withDelegate:(id <KalturaXmlParserDelegate>)aDelegate
{
    assert(!self->_attached);
    
    self.parser = aParser;
    self.delegate = aDelegate;
    
    self->_origDelegate = self.parser.delegate;
    self.parser.delegate = self;
    self->_attached = YES;
}

- (void)detach
{
    if (self->_attached) {
        self.parser.delegate = self->_origDelegate;
        self->_origDelegate = nil;
        self->_attached = NO;
    }
}

- (id)result
{
    return nil;
}

@end

/*
 Class KalturaXmlParserSkipTag
 */
@implementation KalturaXmlParserSkipTag

- init
{
    self = [super init];
    if (self != nil)
    {
        self->_level = 1;
    }
    return self;
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict
{
    self->_level++;
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    self->_level--;
    if (self->_level != 0)
        return;
    
    [self detach];
    [self.delegate parsingComplete];
}

@end

/*
 Class KalturaXmlParserException
 */
@implementation KalturaXmlParserException

@synthesize targetException = _targetException;
@synthesize subParser = _subParser;

- initWithSubParser:(KalturaXmlParserBase*)aSubParser
{
    self = [super init];
    if (self != nil)
    {
        self.subParser = aSubParser;
    }
    
    return self;
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict
{
    if ([elementName compare:@"error"] == NSOrderedSame)
    {
        self.targetException = [[KalturaException alloc] init];
        KalturaXmlParserBase* subParser = [[KalturaXmlParserObject alloc] initWithObject:self.targetException];
        [subParser attachToParser:self.parser withDelegate:self];
    }
    else
    {
        [self.subParser attachToParser:self.parser withDelegate:self];
        [self.subParser parser:parser didStartElement:elementName namespaceURI:namespaceURI qualifiedName:qName attributes:attributeDict];
    }
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    [self detach];
    [self.delegate parsingComplete];
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string
{
    [self.subParser attachToParser:self.parser withDelegate:self];
    [self.subParser parser:parser foundCharacters:string];
}

- (void)parsingComplete
{
    [self detach];
    [self.delegate parsingComplete];
}

- (id)result
{
    if (self.targetException != nil)
    {
        return self.targetException;
    }
    else
    {
        return self.subParser.result;
    }
}

@end

/*
 Class KalturaXmlParserSimpleType
 */
@implementation KalturaXmlParserSimpleType

- (id)result
{
    return self->_value;
}

- (BOOL)boolResult
{
    return [KalturaSimpleTypeParser parseBool:self->_value];
}

- (int)intResult
{
    return [KalturaSimpleTypeParser parseInt:self->_value];
}

- (double)floatResult
{
    return [KalturaSimpleTypeParser parseFloat:self->_value];
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    [self detach];
    [self.delegate parsingComplete];
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string
{
    self->_value = [string copy];
}

@end

/*
 Class KalturaXmlParserObject
 */
@implementation KalturaXmlParserObject

- (id)result
{
    return self->_targetObj;
}

- (id)initWithObject:(KalturaObjectBase*)aObject
{
    self = [super init];
    if (self)
    {
        self->_targetObj = aObject;
    }
    return self;
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict
{
    self->_lastTagCap = [[NSMutableString alloc] initWithFormat:@"%@%@", 
                         [[elementName substringToIndex:1] uppercaseString],
                         [elementName substringFromIndex:1]];
    
    if (self->_targetObj == nil)
    {
        return;
    }
    
    NSString* getPropType = [[NSMutableString alloc] initWithFormat:@"getTypeOf%@", self->_lastTagCap];
    SEL getPropTypeSel = NSSelectorFromString(getPropType);
    self->_lastPropType = KFT_Invalid;
    if ([self->_targetObj respondsToSelector:getPropTypeSel])
    {
        self->_lastPropType = (KalturaFieldType)[self->_targetObj performSelector:getPropTypeSel];
    }
    
    switch (self->_lastPropType)
    {
        case KFT_Invalid:
            self->_subParser = [[KalturaXmlParserSkipTag alloc] init];
            [self->_subParser attachToParser:self.parser withDelegate:self];
            break;
            
        case KFT_Object:
            self->_subParser = [[KalturaXmlParserObject alloc] init];
            [self->_subParser attachToParser:self.parser withDelegate:self];
            break;
            
        case KFT_Array:
            self->_subParser = [[KalturaXmlParserArray alloc] init];
            [self->_subParser attachToParser:self.parser withDelegate:self];
            break;
            
        default:;
    }
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    if (self->_lastTagCap == nil)
    {
        [self detach];
        [self.delegate parsingComplete];
        return;
    }
    
    self->_lastTagCap = nil;
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string
{
    if ([self->_lastTagCap compare:@"ObjectType"] == NSOrderedSame)
    {
        self->_targetObj = [KalturaObjectFactory createByName:string];
    }
    else
    {
        if (self->_lastPropType == KFT_String)
            [self->_targetObj setObjectProperty:self->_lastTagCap withValue:string isSimple:NO];
        else
            [self->_targetObj setObjectProperty:self->_lastTagCap withValue:string isSimple:YES];
    }
}

- (void)parsingComplete
{
    id parseResult = [self->_subParser result];
    if (parseResult != nil)
    {
        [self->_targetObj setObjectProperty:self->_lastTagCap withValue:parseResult isSimple:NO];
    }
    [self->_subParser release];
    self->_subParser = nil;
    self->_lastTagCap = nil;
}

@end

/*
 Class KalturaXmlParserArray
 */
@implementation KalturaXmlParserArray

- (id)init
{
    self = [super init];
    if (self) {
        self->_targetArr = [[NSMutableArray alloc] init];
    }
    
    return self;
}

- (void)dealloc
{
    [self->_targetArr release];
    [super dealloc];
}

- (id)result
{
    return self->_targetArr;
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict
{
    if ([elementName compare:@"item"] != NSOrderedSame)
    {
        @throw @"Unexpected tag";        // XXXXXX
    }
    
    self->_subParser = [[KalturaXmlParserObject alloc] init];
    [self->_subParser attachToParser:self.parser withDelegate:self];
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    [self detach];
    [self.delegate parsingComplete];    
}

- (void)parsingComplete
{
    id parseResult = [self->_subParser result];
    [self->_targetArr addObject:parseResult];
    [self->_subParser release];
    self->_subParser = nil;
}

@end

/*
 Class KalturaXmlParserMultirequest
 */
@implementation KalturaXmlParserMultirequest

- (id)init
{
    self = [super init];
    if (self) {
        self->_subParsers = [[NSMutableArray alloc] init];
    }
    
    return self;
}

- (void)dealloc
{
    [self->_subParsers release];
    [super dealloc];
}

- (void)addSubParser:(KalturaXmlParserBase*)aParser
{
    KalturaXmlParserException* excParser = [[KalturaXmlParserException alloc] initWithSubParser:aParser];
    [self->_subParsers addObject:excParser];
}

- (int)reqCount
{
    return self->_subParsers.count;
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict
{
    if ([elementName compare:@"item"] != NSOrderedSame ||
        self->_reqIndex >= self->_subParsers.count)
    {
        @throw @"Unexpected tag";        // XXXXXX
    }
    
    KalturaXmlParserBase* curParser = [self->_subParsers objectAtIndex:self->_reqIndex];
    [curParser attachToParser:self.parser withDelegate:self];
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    [self detach];
    [self.delegate parsingComplete];    
}

- (void)parsingComplete
{
    KalturaXmlParserBase* curParser = [self->_subParsers objectAtIndex:self->_reqIndex];
    [curParser detach];
    self->_reqIndex++;
}

- (id)result
{
    NSMutableArray* result = [[NSMutableArray alloc] init];
    
    for (KalturaXmlParserBase* curParser in self->_subParsers)
    {
        [result addObject:curParser.result];
    }
    
    return result;
}

@end

/*
 Class KalturaXmlParserSkipPath
 */
@implementation KalturaXmlParserSkipPath

- initWithSubParser:(KalturaXmlParserBase*)aSubParser withPath:(NSArray*)aPath
{
    self = [super init];
    
    self->_subParser = aSubParser;
    self->_path = aPath;
    
    return self;
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict
{
    NSString* expectedElem = (NSString*)[self->_path objectAtIndex:self->_pathPosition];
    if (self->_skipLevel == 0 && [expectedElem compare:elementName] == NSOrderedSame)
    {
        self->_pathPosition++;
        if (self->_pathPosition == self->_path.count)
        {
            [self->_subParser attachToParser:self.parser withDelegate:self];
        }
    }
    else
    {
        self->_skipLevel++;
    }
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName
{
    if (self->_skipLevel != 0)
    {
        self->_skipLevel--;
    }
    else
    {
        self->_pathPosition--;
    }
}

- (void)parsingComplete
{
    self->_pathPosition--;
}

@end

/*
 Class KalturaNSLogger
 */
@implementation KalturaNSLogger

- (void)logMessage:(NSString *)aMsg
{
    NSLog(@"%@", aMsg);
}

@end

/*
 Class KalturaClientConfiguration
 */
@implementation KalturaClientConfiguration

@synthesize clientTag = _clientTag;
@synthesize partnerId = _partnerId;
@synthesize serviceUrl = _serviceUrl;
@synthesize requestTimeout = _requestTimeout;
@synthesize logger = _logger;

- (id)init
{
    self = [super init];
    if (self) {
        self.clientTag = @"objCLib";
        self.partnerId = -1;
        self.serviceUrl = @"http://www.kaltura.com";
        self.requestTimeout = 10;
    }
    
    return self;
}

@end

/*
 Class KalturaClientBase
 */
@interface KalturaClientBase()

- (void)queueService:(NSString*)aService withAction:(NSString*)aAction withParser:(KalturaXmlParserBase*)aParser;
- (NSXMLParser*)issueRequestWithQuery:(NSString*)query;
- (void)logFormat:(NSString *)aFormat, ...;

@end

@implementation KalturaClientBase

@synthesize config = _config;
@synthesize ks = _ks;
@synthesize apiVersion = _apiVersion;
@synthesize params = _params;

- (id)initWithConfig:(KalturaClientConfiguration*)aConfig
{
    self = [super init];
    if (self) {
        self->_params = [[KalturaParams alloc] init];
        self->_config = aConfig;
    }
    
    return self;
}

- (void)dealloc {
    [self->_params release];
    [super dealloc];
}

- (void)addRequestDefaultParams
{    
    NSString* paramsPartnerId = [self->_params get:@"partnerId"];
    if ((paramsPartnerId == nil || [paramsPartnerId compare:@"-1"] == NSOrderedSame) &&
        self.config.partnerId != -1)
    {
        NSString* strPartnerId = [NSString stringWithFormat:@"%d", self.config.partnerId];
        [self->_params addIfDefinedKey:@"partnerId" withString:strPartnerId];
    }
    [self->_params addIfDefinedKey:@"ks" withString:self.ks];
}

- (void)addGlobalDefaultParams
{
    [self->_params addIfDefinedKey:@"apiVersion" withString:self.apiVersion];
    [self->_params addIfDefinedKey:@"format" withString:KALTURA_SERVICE_FORMAT_XML];
    [self->_params addIfDefinedKey:@"clientTag" withString:self.config.clientTag];
}

- (void)parseResponseWithXmlParser:(NSXMLParser*)aXmlParser withParser:(KalturaXmlParserBase*)aParser
{
    NSDate *parseStart = [NSDate date];
    KalturaXmlParserSkipPath* skipParser = [[KalturaXmlParserSkipPath alloc] initWithSubParser:aParser withPath:
                                            [NSArray arrayWithObjects: @"xml", @"result", nil]];        
    [skipParser attachToParser:aXmlParser withDelegate:nil];
    [aXmlParser parse];    
    [self logFormat:@"response parsing took %.2f seconds", -[parseStart timeIntervalSinceNow]];
}

- (void)queueService:(NSString*)aService withAction:(NSString*)aAction withParser:(KalturaXmlParserBase*)aParser
{
    [self addRequestDefaultParams];
    
    if (self->_multiReqParser != nil)
    {
        [self->_params addIfDefinedKey:@"service" withString:aService];
        [self->_params addIfDefinedKey:@"action" withString:aAction];
        [self->_multiReqParser addSubParser:aParser];
        [self->_params setPrefix:[NSString stringWithFormat:@"%d:", self->_multiReqParser.reqCount + 1]];
        return;
    }
    
    NSString* query = [NSString stringWithFormat:@"service=%@&action=%@", aService, aAction];
    
    NSXMLParser* xmlParser = [self issueRequestWithQuery:query];
    
    KalturaXmlParserException* excParser = [[KalturaXmlParserException alloc] initWithSubParser:aParser];
    [self parseResponseWithXmlParser:xmlParser withParser:excParser];
    
    if (excParser.targetException != nil)
    {
        @throw excParser.targetException;
    }
}

- (NSString*)queueServeService:(NSString*)aService withAction:(NSString*)aAction
{
    [self->_params addIfDefinedKey:@"service" withString:aService];
    [self->_params addIfDefinedKey:@"action" withString:aAction];
    [self addRequestDefaultParams];
    [self addGlobalDefaultParams];
    NSString* query = [self->_params getQueryString];
    NSString* url = [NSString stringWithFormat:@"%@%@?%@", self.config.serviceUrl, KALTURA_SERVICE_BASE_URL, query];
    return url;
}

- (void)queueVoidService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
}

- (BOOL)queueBoolService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    return parser.boolResult;
}

- (int)queueIntService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    return parser.intResult;
}

- (double)queueFloatService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    return parser.floatResult;
}

- (NSString*)queueStringService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    return [parser result];
}

- (id)queueObjectService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserObject* parser = [[KalturaXmlParserObject alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    return parser.result;
}

- (NSMutableArray*)queueArrayService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserArray* parser = [[KalturaXmlParserArray alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    return parser.result;
}

- (NSXMLParser*)issueRequestWithQuery:(NSString*)query
{
    [self->_params setPrefix:@""];
    [self addGlobalDefaultParams];
    [self->_params sign];
    
    NSString* urlStr = [NSString stringWithFormat:@"%@%@?%@", self.config.serviceUrl, KALTURA_SERVICE_BASE_URL, query];
    [self logFormat:@"request url: %@", urlStr];
    NSURL *url = [NSURL URLWithString:urlStr];
    
    ASIFormDataRequest *request = [ASIFormDataRequest requestWithURL:url];
    request.timeOutSeconds = self.config.requestTimeout;
    [self->_params addToRequest:request];
    
    NSDate *apiStart = [NSDate date];
    [request startSynchronous];
    [self logFormat:@"api call took %.2f seconds", -[apiStart timeIntervalSinceNow]];        
    
    NSError *error = [request error];
    if (error)
    {
        // TODO: Handle this
    }
    
    NSData* data = [request responseData];
    if (data.length < 1024)
        [self logFormat:@"response (xml): %@", data];
    else
        [self logFormat:@"response (xml): %d bytes", data.length];        
    
    NSXMLParser* xmlParser = [[NSXMLParser alloc] initWithData:data];
    return xmlParser;
}

- (void)startMultiRequest
{
    if (self->_multiReqParser)
    {
        @throw @"Already in multirequest";
    }
    
    self->_multiReqParser = [[KalturaXmlParserMultirequest alloc] init];
    [self->_params setPrefix:@"1:"];
}

- (NSArray*)doMultiRequest
{
    if (self->_multiReqParser == nil)
    {
        @throw @"Not in multirequest";
    }
    
    NSString* query = [NSString stringWithFormat:@"service=multirequest"];
    
    NSXMLParser* xmlParser = [self issueRequestWithQuery:query];
    
    [self parseResponseWithXmlParser:xmlParser withParser:self->_multiReqParser];
    
    id result = [self->_multiReqParser result];
    
    [self->_multiReqParser release];
    self->_multiReqParser = nil;
    
    return result;
}

- (void)logFormat:(NSString *)aFormat, ...
{
    if (self.config.logger == nil)
    {
        return;
    }
    
    va_list argumentList;
    va_start(argumentList, aFormat);
    
    NSString *string = [[NSString alloc] initWithFormat:aFormat arguments:argumentList];
    
    va_end(argumentList);
    
    [self.config.logger logMessage:string];
    
    [string release];
}

+ (NSString*)generateSessionWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    int rand = arc4random() % 0x10000;
    NSDate* date = [NSDate date];
    NSTimeInterval ts = [date timeIntervalSince1970];
    int ksExpiry = (int)ts + aExpiry;
    NSString* ksFields = [NSString stringWithFormat:@"%d;%d;%d;%d;%d;%@;%@", aPartnerId, aPartnerId, ksExpiry, aType, rand, aUserId, aPrivileges];
        
    NSMutableString* ksWithSig = [NSMutableString stringWithCapacity:(CC_SHA1_DIGEST_LENGTH * 2 + 1 + ksFields.length)];
    
    [KalturaClientBase appendSessionSigWithSecret:aSecret withFields:ksFields withOutput:ksWithSig];
    [ksWithSig appendString:@"|"];
    [ksWithSig appendString:ksFields];
    
    NSString* result = [ASIHTTPRequest base64forData:[ksWithSig dataUsingEncoding:NSUTF8StringEncoding]];
    
    return result;
}

+ (void)appendSessionSigWithSecret:(NSString*)aSecret withFields:(NSString*)aKsFields withOutput:(NSMutableString*)aOutput
{
    unsigned char sha1Hash[CC_SHA1_DIGEST_LENGTH];
    CC_SHA1_CTX sha1Ctx;
    
    CC_SHA1_Init(&sha1Ctx);
    const char* secretPtr = [aSecret UTF8String];
    CC_SHA1_Update(&sha1Ctx, secretPtr, strlen(secretPtr));
    const char* fieldsPtr = [aKsFields UTF8String];
    CC_SHA1_Update(&sha1Ctx, fieldsPtr, strlen(fieldsPtr));
    
    CC_SHA1_Final(sha1Hash, &sha1Ctx);
    
    for (int i = 0; i < CC_SHA1_DIGEST_LENGTH; i++)
        [aOutput appendFormat:@"%02x", sha1Hash[i]];
}

@end

/*
 Class KalturaServiceBase
 */
@implementation KalturaServiceBase

@synthesize client = _client;

- (id)initWithClient:(KalturaClientBase*)aClient
{
    self = [super init];
    if (self) {
        self.client = aClient;
    }
    
    return self;
}

@end

/*
 Class KalturaClientPlugin
 */
@implementation KalturaClientPlugin
@end
