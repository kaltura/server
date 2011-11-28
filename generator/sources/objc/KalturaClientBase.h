#import <Foundation/Foundation.h>
#import <Foundation/NSXMLParser.h>

#define KALTURA_UNDEF_BOOL      ((BOOL)CHAR_MIN)
#define KALTURA_UNDEF_INT       INT_MIN
#define KALTURA_UNDEF_FLOAT     NAN
#define KALTURA_UNDEF_STRING    (nil)

#define KALTURA_NULL_BOOL   ((BOOL)CHAR_MAX)
#define KALTURA_NULL_INT    INT_MAX
#define KALTURA_NULL_FLOAT  INFINITY
#define KALTURA_NULL_STRING (@"__null_string__")

@class ASIFormDataRequest;

typedef enum 
{
    KFT_Invalid,
    KFT_Bool,
    KFT_Int,
    KFT_Float,
    KFT_String,
    KFT_Object,
    KFT_Array,
} KalturaFieldType;

@class KalturaParams;

/*
 Class KalturaSimpleTypeParser
 */
@interface KalturaSimpleTypeParser : NSObject

+ (BOOL)parseBool:(NSString*)aStr;
+ (int)parseInt:(NSString*)aStr;
+ (double)parseFloat:(NSString*)aStr;

@end

/*
 Class KalturaObjectBase
 */
@interface KalturaObjectBase : NSObject

- (void)setObjectProperty:(NSString*)aPropNameCap withValue:(id)aValue isSimple:(BOOL)aIsSimple;
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper;

@end

/*
 Class KalturaException
 */
@interface KalturaException : KalturaObjectBase

@property (nonatomic, copy) NSString* code;
@property (nonatomic, copy) NSString* message;

@end

/*
 Class KalturaObjectFactory
 */
@interface KalturaObjectFactory : NSObject

+ (KalturaObjectBase*)createByName:(NSString*)aName;

@end

///////////////////////// Request Building /////////////////////////

/*
 Class KalturaParams
 */
@interface KalturaParams : NSObject
{
    NSMutableArray* _params;
    NSMutableArray* _files;
    NSMutableString* _prefix;
}

- (void)setPrefix:(NSString*)aPrefix;
- (NSString*)get:(NSString*)aKey;
- (void)putKey:(NSString*)aKey withString:(NSString*)aVal;
- (void)putNullKey:(NSString*)aKey;
- (void)addIfDefinedKey:(NSString*)aKey withFileName:(NSString*)aFileName;
- (void)addIfDefinedKey:(NSString*)aKey withBool:(BOOL)aVal;
- (void)addIfDefinedKey:(NSString*)aKey withInt:(int)aVal;
- (void)addIfDefinedKey:(NSString*)aKey withFloat:(double)aVal;
- (void)addIfDefinedKey:(NSString*)aKey withString:(NSString*)aVal;
- (void)addIfDefinedKey:(NSString*)aKey withObject:(KalturaObjectBase*)aVal;
- (void)addIfDefinedKey:(NSString*)aKey withArray:(NSArray*)aVal;
- (void)sign;
- (void)addToRequest:(ASIFormDataRequest*)aRequest;
- (NSString*)getQueryString;

@end


///////////////////////// Response Parsing /////////////////////////

/*
 Protocol KalturaXmlParserDelegate
 */
@protocol KalturaXmlParserDelegate

- (void)parsingComplete;

@end

/*
 Class KalturaXmlParserBase
 */
@interface KalturaXmlParserBase : NSObject <NSXMLParserDelegate>
{
    id <NSXMLParserDelegate> _origDelegate;
    BOOL _attached;
}

@property (nonatomic, assign) NSXMLParser* parser;
@property (nonatomic, assign) id <KalturaXmlParserDelegate> delegate;

- (void)attachToParser:(NSXMLParser*)aParser withDelegate:(id <KalturaXmlParserDelegate>)aDelegate;
- (void)detach;
- (id)result;

@end

/*
 Class KalturaXmlParserSkipTag
 */
@interface KalturaXmlParserSkipTag : KalturaXmlParserBase
{
    int _level;
}
@end

/*
 Class KalturaXmlParserException
 */
@interface KalturaXmlParserException : KalturaXmlParserBase <KalturaXmlParserDelegate>

- initWithSubParser:(KalturaXmlParserBase*)aSubParser;

@property (nonatomic, assign) KalturaException* targetException;
@property (nonatomic, assign) KalturaXmlParserBase* subParser;

@end

/*
 Class KalturaXmlParserSimpleType
 */
@interface KalturaXmlParserSimpleType : KalturaXmlParserBase
{
    NSString* _value;
}

- (BOOL)boolResult;
- (int)intResult;
- (double)floatResult;

@end

/*
 Class KalturaXmlParserObject
 */
@interface KalturaXmlParserObject : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    KalturaObjectBase* _targetObj;
    KalturaXmlParserBase* _subParser;
    NSString* _lastTagCap;
    KalturaFieldType _lastPropType;
}

- (id)initWithObject:(KalturaObjectBase*)aObject;

@end

/*
 Class KalturaXmlParserArray
 */
@interface KalturaXmlParserArray : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    NSMutableArray* _targetArr;
    KalturaXmlParserBase* _subParser;
}

@end

/*
 Class KalturaXmlParserMultirequest
 */
@interface KalturaXmlParserMultirequest : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    NSMutableArray* _subParsers;
    int _reqIndex;
}

- (void)addSubParser:(KalturaXmlParserBase*)aParser;
- (int)reqCount;

@end

/*
 Class KalturaXmlParserSkipPath
 */
@interface KalturaXmlParserSkipPath : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    KalturaXmlParserBase* _subParser;
    NSArray* _path;
    int _pathPosition;
    int _skipLevel;
}

- initWithSubParser:(KalturaXmlParserBase*)aSubParser withPath:(NSArray*)aPath;

@end

/*
 Protocol KalturaLogger
 */
@protocol KalturaLogger

- (void)logMessage:(NSString*)aMsg;

@end

/*
 Class KalturaNSLogger
 */
@interface KalturaNSLogger: NSObject <KalturaLogger>

@end

/*
 Class KalturaClientConfiguration
 */
@interface KalturaClientConfiguration : NSObject

@property (nonatomic, copy) NSString* serviceUrl;
@property (nonatomic, assign) int partnerId;
@property (nonatomic, copy) NSString* clientTag;
@property (nonatomic, assign) int requestTimeout;
@property (nonatomic, assign) id<KalturaLogger> logger;

@end

/*
 Class KalturaClientBase
 */
@interface KalturaClientBase : NSObject
{
    KalturaXmlParserMultirequest* _multiReqParser;
}

@property (nonatomic, retain) KalturaClientConfiguration* config;
@property (nonatomic, copy) NSString* ks;
@property (nonatomic, copy) NSString* apiVersion;
@property (nonatomic, assign) KalturaParams* params;

- (id)initWithConfig:(KalturaClientConfiguration*)aConfig;
- (void)startMultiRequest;
- (NSArray*)doMultiRequest;
+ (NSString*)generateSessionWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges;

- (NSString*)queueServeService:(NSString*)aService withAction:(NSString*)aAction;
- (void)queueVoidService:(NSString*)aService withAction:(NSString*)aAction;
- (BOOL)queueBoolService:(NSString*)aService withAction:(NSString*)aAction;
- (int)queueIntService:(NSString*)aService withAction:(NSString*)aAction;
- (double)queueFloatService:(NSString*)aService withAction:(NSString*)aAction;
- (NSString*)queueStringService:(NSString*)aService withAction:(NSString*)aAction;
- (id)queueObjectService:(NSString*)aService withAction:(NSString*)aAction;
- (NSMutableArray*)queueArrayService:(NSString*)aService withAction:(NSString*)aAction;
+ (void)appendSessionSigWithSecret:(NSString*)aSecret withFields:(NSString*)aKsFields withOutput:(NSMutableString*)aOutput;

@end

/*
 Class KalturaServiceBase
 */
@interface KalturaServiceBase : NSObject

@property (nonatomic, assign) KalturaClientBase* client;

- (id)initWithClient:(KalturaClientBase*)aClient;

@end

/*
 Class KalturaClientPlugin
 */
@interface KalturaClientPlugin : NSObject
@end
