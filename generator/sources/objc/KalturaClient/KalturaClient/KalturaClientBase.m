// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
#import <CommonCrypto/CommonDigest.h>
#import "ASIFormDataRequest.h"
#import "KalturaClientBase.h"
#import "KalturaXmlParsers.h"

/*
 String constants
 */
static NSString* const KalturaServiceBaseUrl = @"/api_v3/index.php";
static NSString* const KalturaServiceFormatXml = @"2";
NSString* const KalturaClientErrorDomain = @"KalturaClientErrorDomain";

/*
 Class KalturaClientException
 */
@implementation KalturaClientException
@end

/*
 Class KalturaSimpleTypeParser
 */
@implementation KalturaSimpleTypeParser

+ (BOOL)parseBool:(NSString*)aStr
{
    if (aStr == nil)
        return NO;
    if ([aStr compare:@"1"] != NSOrderedSame)
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

- (void)dealloc
{
    [self->_code release];
    [self->_message release];
    [super dealloc];
}

- (KalturaFieldType)getTypeOfCode
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMessage
{
    return KFT_String;
}

- (NSError*)error
{
    return [NSError errorWithDomain:KalturaClientErrorDomain code:KalturaClientErrorAPIException userInfo:[NSDictionary dictionaryWithObjectsAndKeys:self.message, NSLocalizedDescriptionKey, self.code, @"ExceptionCode", nil]];
}

@end

/*
 Class KalturaObjectFactory
 */
@implementation KalturaObjectFactory

+ (KalturaObjectBase*)createByName:(NSString*)aName withDefaultType:(NSString*)aDefaultType
{
    Class objClass = NSClassFromString(aName);
    if (objClass == nil)
    {
		objClass = NSClassFromString(aDefaultType);
		if (objClass == nil)
		{
			return nil;
		}
    }
    if (![objClass isSubclassOfClass:[KalturaObjectBase class]])
    {
        return nil;
    }
    return [[objClass alloc] init];
}

@end

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
    if (self == nil)
        return nil;

    self.key = aKey;
    self.value = aValue;

    return self;
}

- (void)dealloc
{
    [self->_key release];
    [self->_value release];
    [super dealloc];
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
    if (self == nil)
        return nil;

    self.key = aKey;
    self.value = aValue;

    return self;
}

- (void)dealloc
{
    [self->_key release];
    [self->_value release];
    [super dealloc];
}

@end

/*
 Class KalturaParams
 */
@implementation KalturaParams

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;

    self->_params = [[NSMutableArray alloc] init];
    self->_files = [[NSMutableArray alloc] init];
    self->_prefix = [[NSMutableString alloc] init];
    
    return self;
}

- (void)dealloc 
{
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
    NSRange range = NSMakeRange(curLength - aKey.length - 1, aKey.length + 1);
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
    KalturaParam* param = nil;
    
    if (self->_prefix.length != 0)
    {
        aKey = [[NSString alloc] initWithFormat:@"%@%@", self->_prefix, aKey];
    }
    param = [[KalturaParam alloc] initWithKey:aKey withValue:aVal];
    if (self->_prefix.length != 0)
    {
        [aKey release];
    }
    [self->_params addObject:param];
    [param release];
}

- (void)putNullKey:(NSString*)aKey
{
    NSString* nullKey = [[NSString alloc] initWithFormat:@"%@%@", aKey, @"__null"];
    [self putKey:nullKey withString:@""];
    [nullKey release];
}

- (void)addIfDefinedKey:(NSString*)aKey withFileName:(NSString*)aFileName;
{
    KalturaFile* param = nil;
    
    if (aFileName == nil)
        return;
    
    if (self->_prefix.length != 0)
    {
        aKey = [[NSString alloc] initWithFormat:@"%@%@", self->_prefix, aKey];
    }
    param = [[KalturaFile alloc] initWithKey:aKey withFileName:aFileName];
    if (self->_prefix.length != 0)
    {
        [aKey release];
    }
    [self->_files addObject:param];
    [param release];
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
    
    if ([aVal isMemberOfClass:[KalturaObjectBase class]])
    {
        [self putNullKey:aKey];
    }
    else
    {
        [self pushKey:aKey]; 

        [aVal toParams:self isSuper:NO];
    
        [self popKey:aKey]; 
    }
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
            NSString* curKey = [[NSString alloc] initWithFormat:@"%d", index];
            [self addIfDefinedKey:curKey withObject:[aVal objectAtIndex:index]];
            [curKey release];
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

    NSMutableString* kalSig = [[NSMutableString alloc] initWithCapacity:(CC_MD5_DIGEST_LENGTH * 2)];
    for (int i = 0; i < CC_MD5_DIGEST_LENGTH; i++)
        [kalSig appendFormat:@"%02x", md5Hash[i]];
    
    [self putKey:@"kalsig" withString:kalSig];
    
    [kalSig release];
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

+ (NSString*)allocUrlEncodedString:(NSString*)origStr
{
    return (NSString*)CFURLCreateStringByAddingPercentEscapes(
        NULL, 
        (CFStringRef)origStr, 
        NULL, 
        (CFStringRef)@"!*'();:@&=+$,/?%#[] \"\\<>{}|^~`", 
        kCFStringEncodingUTF8);
}

- (void)appendQueryString:(NSMutableString*)output
{
    while (self->_params.count)
    {
        KalturaParam* curParam = [self->_params lastObject];
        NSString *encodedKey = [KalturaParams allocUrlEncodedString:curParam.key];
        NSString *encodedVal = [KalturaParams allocUrlEncodedString:curParam.value];
        [output appendFormat:@"%@=%@&", encodedKey, encodedVal];
        [encodedVal release];
        [encodedKey release];
        [self->_params removeLastObject];
    }
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
 Class KalturaServiceBase
 */
@implementation KalturaServiceBase

@synthesize client = _client;

- (id)initWithClient:(KalturaClientBase*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;

    self.client = aClient;
    
    return self;
}

@end

/*
 Class KalturaClientPlugin
 */
@implementation KalturaClientPlugin
@end

/*
 Class KalturaClientConfiguration
 */
@implementation KalturaClientConfiguration

@synthesize serviceUrl = _serviceUrl;
@synthesize clientTag = _clientTag;
@synthesize partnerId = _partnerId;
@synthesize requestTimeout = _requestTimeout;
@synthesize logger = _logger;
@synthesize requestHeaders = _requestHeaders;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;

    self.clientTag = @"objCLib:@DATE@";
    self.partnerId = -1;
    self.serviceUrl = @"http://www.kaltura.com";
    self.requestTimeout = 120;
	self.requestHeaders = [[NSDictionary alloc] init];
    
    return self;
}

- (void)dealloc
{
    [self->_serviceUrl release];
    [self->_clientTag release];
    [self->_logger release];
    [self->_requestHeaders release];
    [super dealloc];
}

@end

/*
 Class KalturaClientBase
 */
@interface KalturaClientBase()

- (id)queueService:(NSString*)aService withAction:(NSString*)aAction withParser:(KalturaXmlParserBase*)aParser;
- (id)issueRequestWithQuery:(NSString*)aFormat, ...;
- (void)logFormat:(NSString *)aFormat, ...;
+ (void)appendSessionSigWithSecret:(NSString*)aSecret withFields:(NSString*)aKsFields withOutput:(NSMutableString*)aOutput;

@end

@implementation KalturaClientBase

@synthesize config = _config;
@synthesize error = _error;
@synthesize delegate = _delegate;
@synthesize uploadProgressDelegate = _uploadProgressDelegate;
@synthesize downloadProgressDelegate = _downloadProgressDelegate;
@synthesize ks = _ks;
@synthesize apiVersion = _apiVersion;
@synthesize params = _params;
@synthesize responseHeaders = _responseHeaders;

- (id)initWithConfig:(KalturaClientConfiguration*)aConfig
{
    self = [super init];
    if (self == nil)
        return nil;

    self->_params = [[KalturaParams alloc] init];
    self.config = aConfig;
    
    return self;
}

- (void)dealloc 
{
    [self cancelRequest];
    [self->_error release];
    [self->_ks release];
    [self->_apiVersion release];
    [self->_params release];
    [self->_config release];
    [super dealloc];
}

- (void)cancelRequest
{
    [self->_request clearDelegatesAndCancel];
    [self->_request release];
    self->_request = nil;
    [self->_responseHeaders release];
	self->_responseHeaders = nil;
    [self->_apiStartTime release];
    self->_apiStartTime = nil;
	self->_skipParser.delegate = nil;
    [self->_skipParser release];
    self->_skipParser = nil;
    [self->_reqParser release];
    self->_reqParser = nil;
    self->_xmlParser.delegate = nil;    // the xmlParser could be retained if we're in xmlParseChunk
    [self->_xmlParser release];
    self->_xmlParser = nil;
    self->_isMultiRequest = NO;
    [self->_params setPrefix:@""];
}

- (id)completeRequest
{
    if (self->_apiStartTime != nil)
        [self logFormat:@"api call took %.2f seconds", -[self->_apiStartTime timeIntervalSinceNow]];
    
    id result = [[self->_reqParser result] retain];
    [self cancelRequest];
    [result autorelease];
    
    if ([result isKindOfClass:[NSError class]])
    {
        self.error = result;
        return nil;
    }
    
    return result;
}

- (void)addRequestDefaultParams
{    
    NSString* paramsPartnerId = [self->_params get:@"partnerId"];
    if ((paramsPartnerId == nil || [paramsPartnerId compare:@"-1"] == NSOrderedSame) &&
        self.config.partnerId != -1)
    {
        NSString* strPartnerId = [[NSString alloc] initWithFormat:@"%d", self.config.partnerId];
        [self->_params addIfDefinedKey:@"partnerId" withString:strPartnerId];
        [strPartnerId release];
    }
    [self->_params addIfDefinedKey:@"ks" withString:self.ks];
}

- (void)addGlobalParamsAndSign
{
    [self->_params setPrefix:@""];
    [self->_params addIfDefinedKey:@"apiVersion" withString:self.apiVersion];
    [self->_params addIfDefinedKey:@"format" withString:KalturaServiceFormatXml];
    [self->_params addIfDefinedKey:@"clientTag" withString:self.config.clientTag];
    [self->_params addIfDefinedKey:@"ignoreNull" withString:@"1"];
    [self->_params sign];
}

- (NSString*)queueServeService:(NSString*)aService withAction:(NSString*)aAction
{
    self.error = nil;
    [self->_params addIfDefinedKey:@"service" withString:aService];
    [self->_params addIfDefinedKey:@"action" withString:aAction];
    [self addRequestDefaultParams];
    [self addGlobalParamsAndSign];
    
    NSMutableString* result = [[NSMutableString alloc] initWithFormat:@"%@%@?", self.config.serviceUrl, KalturaServiceBaseUrl];
    [self->_params appendQueryString:result];
    [result autorelease];
    return result;
}

- (void)setupRequestWithQuery:(NSString*)aFormat withArguments:(va_list)vaArgs
{
    NSString* query = [[NSString alloc] initWithFormat:aFormat arguments:vaArgs];
    NSString* urlStr = [[NSString alloc] initWithFormat:@"%@%@?%@", self.config.serviceUrl, KalturaServiceBaseUrl, query];
    NSURL *url = [[NSURL alloc] initWithString:urlStr];

    [self logFormat:@"request url: %@", urlStr];

    self->_request = [[ASIFormDataRequest alloc] initWithURL:url];

    [url release];
    [urlStr release];
    [query release];

    self->_request.delegate = self;
    self->_request.timeOutSeconds = self.config.requestTimeout;
    self->_request.shouldWaitToInflateCompressedResponses = NO;
	self->_request.uploadProgressDelegate = self.uploadProgressDelegate;
	self->_request.downloadProgressDelegate = self.downloadProgressDelegate;

	for(NSString* key in self.config.requestHeaders)
		[self->_request addRequestHeader:key value:[self.config.requestHeaders objectForKey:key]];

    [self addGlobalParamsAndSign];
    [self->_params addToRequest:self->_request];
}

- (void)setupXmlParser
{
    NSArray* path = [[NSArray alloc] initWithObjects: @"xml", @"result", nil];
    self->_skipParser = [[KalturaXmlParserSkipPath alloc] initWithSubParser:self->_reqParser withPath:path];
    [path release];
    
    self->_xmlParser = [[KalturaLibXmlWrapper alloc] init];
    [self->_skipParser attachToParser:self->_xmlParser withDelegate:self];
}

- (id)issueRequestWithQuery:(NSString*)aFormat, ...
{
    [self setupXmlParser];    
    
    va_list vaArgs;
    va_start(vaArgs, aFormat);
    [self setupRequestWithQuery:aFormat withArguments:vaArgs];
    va_end(vaArgs);
    
    self->_apiStartTime = [[NSDate alloc] init];
    
    if (self.delegate == nil)
    {
        [self->_request startSynchronous];
        return [self completeRequest];
    }
    else
    {
        [self->_request startAsynchronous];
        return nil;
    }
}

- (void)queueMultiRequestService:(NSString*)aService withAction:(NSString*)aAction withParser:(KalturaXmlParserBase*)aParser
{
    KalturaXmlParserMultirequest* multiReqParser = (KalturaXmlParserMultirequest*)self->_reqParser;
    [self->_params addIfDefinedKey:@"service" withString:aService];
    [self->_params addIfDefinedKey:@"action" withString:aAction];
    [multiReqParser addSubParser:aParser];
    
    NSString* newPrefix = [[NSString alloc] initWithFormat:@"%d:", multiReqParser.reqCount + 1];
    [self->_params setPrefix:newPrefix];
    [newPrefix release];
}

- (id)queueService:(NSString*)aService withAction:(NSString*)aAction withParser:(KalturaXmlParserBase*)aParser
{
    [self addRequestDefaultParams];
    
    KalturaXmlParserException* excParser = [[KalturaXmlParserException alloc] initWithSubParser:aParser];

    if (self->_isMultiRequest)
    {
        [self queueMultiRequestService:aService withAction:aAction withParser:excParser];
        [excParser release];
        return nil;
    }

    self.error = nil;
    self->_reqParser = [excParser retain];
    [excParser release];
    
    return [self issueRequestWithQuery:@"service=%@&action=%@", aService, aAction];
}

- (void)startMultiRequest
{
    if (self->_isMultiRequest)
    {
        @throw [KalturaClientException exceptionWithName:@"DoubleStartMultiReq" reason:@"startMultiRequest called while already started" userInfo:nil];
    }
    
    self.error = nil;
    self->_reqParser = [[KalturaXmlParserMultirequest alloc] init];
    [self->_params setPrefix:@"1:"];
    self->_isMultiRequest = YES;
}

- (NSArray*)doMultiRequest
{
    if (!self->_isMultiRequest)
    {
        @throw [KalturaClientException exceptionWithName:@"EndWithoutMultiReq" reason:@"doMultiRequest called while not in multirequest" userInfo:nil];
    }
    
    KalturaXmlParserMultirequest* multiReqParser = (KalturaXmlParserMultirequest*)self->_reqParser;
    if (![multiReqParser reqCount])
    {
        [self cancelRequest];
        
        if (self.delegate == nil)
        {
            return [NSArray array];
        }
        else
        {
            [self.delegate requestFinished:self withResult:[NSArray array]];
            return nil;
        }
    }
        
    return [self issueRequestWithQuery:@"service=multirequest"];
}

- (void)queueVoidService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
}

- (BOOL)queueBoolService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    id result = [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
    return [KalturaSimpleTypeParser parseBool:result];
}

- (int)queueIntService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    id result = [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
    return [KalturaSimpleTypeParser parseInt:result];
}

- (double)queueFloatService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    id result = [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
    return [KalturaSimpleTypeParser parseFloat:result];
}

- (NSString*)queueStringService:(NSString*)aService withAction:(NSString*)aAction
{
    KalturaXmlParserSimpleType* parser = [[KalturaXmlParserSimpleType alloc] init];
    id result = [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
    return result;
}

- (id)queueObjectService:(NSString*)aService withAction:(NSString*)aAction withExpectedType:(NSString*)aExpectedType
{
    KalturaXmlParserObject* parser = [[KalturaXmlParserObject alloc] initWithExpectedType:aExpectedType];
    id result = [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
    return result;
}

- (NSMutableArray*)queueArrayService:(NSString*)aService withAction:(NSString*)aAction withExpectedType:(NSString*)aExpectedType
{
    KalturaXmlParserArray* parser = [[KalturaXmlParserArray alloc] initWithExpectedType:aExpectedType];
    id result = [self queueService:aService withAction:aAction withParser:parser];
    [parser release];
    return result;
}

- (void)failRequestWithError:(NSError*)aError
{
    self.error = aError;
    [self cancelRequest];
    [self.delegate requestFailed:self];
}

- (void)request:(ASIHTTPRequest *)request didReceiveResponseHeaders:(NSDictionary *)responseHeaders
{
    self->_responseHeaders = [responseHeaders retain];
    
    NSString* serverName = [responseHeaders objectForKey:@"X-Me"];
    NSString* sessionName = [responseHeaders objectForKey:@"X-Kaltura-Session"];
    [self logFormat:@"server: %@, session: %@", serverName, sessionName];
    
    int statusCode = [request responseStatusCode];
    
    if (statusCode != 200)
    {
        NSNumber *statusCodeNum = [NSNumber numberWithInt:statusCode];
        NSError *nsError = [NSError errorWithDomain:KalturaClientErrorDomain code:KalturaClientErrorInvalidHttpCode userInfo:[NSDictionary dictionaryWithObjectsAndKeys: @"Got invalid HTTP status", NSLocalizedDescriptionKey, statusCodeNum, @"StatusCode", nil]];         
        [self failRequestWithError:nsError];
    }
}

- (void)request:(ASIHTTPRequest *)request didReceiveData:(NSData *)data
{
    [self->_xmlParser processData:data];    
}

- (void)requestFinished:(ASIHTTPRequest *)request
{
    [self->_xmlParser noMoreData];
}

- (void)requestFailed:(ASIHTTPRequest *)request
{
    [self failRequestWithError:[request error]];
}

- (void)parsingFinished:(KalturaXmlParserBase*)aParser
{
    if (self.delegate == nil)
        return;
    
    id result = [self completeRequest];
    if (self.error != nil)
        [self.delegate requestFailed:self];
    else
        [self.delegate requestFinished:self withResult:result];
}

- (void)parsingFailed:(KalturaXmlParserBase*)aParser
{
    [self failRequestWithError:[aParser error]];
}

- (void)logFormat:(NSString *)aFormat, ...
{
    if (self.config.logger == nil)
    {
        return;
    }
    
    va_list vaArgs;
    va_start(vaArgs, aFormat);    
    NSString *string = [[NSString alloc] initWithFormat:aFormat arguments:vaArgs];
    va_end(vaArgs);
    
    [self.config.logger logMessage:string];
    
    [string release];
}

+ (NSString*)generateSessionWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges
{
    int rand = arc4random() % 0x10000;
    int ksExpiry = (int)[[NSDate date] timeIntervalSince1970] + aExpiry;
    NSString* ksFields = [[NSString alloc] initWithFormat:@"%d;%d;%d;%d;%d;%@;%@", aPartnerId, aPartnerId, ksExpiry, aType, rand, aUserId, aPrivileges];
        
    NSMutableString* ksWithSig = [[NSMutableString alloc] initWithCapacity:(CC_SHA1_DIGEST_LENGTH * 2 + 1 + ksFields.length)];
    
    [KalturaClientBase appendSessionSigWithSecret:aSecret withFields:ksFields withOutput:ksWithSig];
    [ksWithSig appendString:@"|"];
    [ksWithSig appendString:ksFields];
    
    [ksFields release];
    
    NSString* result = [ASIHTTPRequest base64forData:[ksWithSig dataUsingEncoding:NSUTF8StringEncoding]];
    
    [ksWithSig release];
    
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
