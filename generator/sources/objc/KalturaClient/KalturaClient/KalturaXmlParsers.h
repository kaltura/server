/*
 Forward declarations
 */
@class KalturaLibXmlWrapper;
@class KalturaXmlParserBase;
@class KalturaObjectBase;
@class KalturaException;

/*
 Protocol KalturaXmlParserDelegate
 */
@protocol KalturaLibXmlWrapperDelegate <NSObject>

@optional

- (void)parser:(KalturaLibXmlWrapper *)aParser didStartElement:(NSString *)aElementName;
- (void)parser:(KalturaLibXmlWrapper *)aParser didEndElement:(NSString *)aElementName;
- (void)parser:(KalturaLibXmlWrapper *)aParser foundCharacters:(NSString *)aString;
- (void)parser:(KalturaLibXmlWrapper *)aParser parseErrorOccurred:(NSError *)aParseError;

@end

/*
 Class KalturaLibXmlWrapper
 */
@interface KalturaLibXmlWrapper : NSObject
{
    struct _xmlParserCtxt* _xmlCtx;
    NSMutableString* _foundChars;
}

@property (nonatomic, assign) id<KalturaLibXmlWrapperDelegate> delegate;

- (void)processData:(NSData*)aData;
- (void)noMoreData;

@end

/*
 Protocol KalturaXmlParserDelegate
 */
@protocol KalturaXmlParserDelegate <NSObject>

- (void)parsingFinished:(KalturaXmlParserBase*)aParser;
- (void)parsingFailed:(KalturaXmlParserBase*)aParser;

@end

/*
 Class KalturaXmlParserBase
 */
@interface KalturaXmlParserBase : NSObject <KalturaLibXmlWrapperDelegate>
{
    id <KalturaLibXmlWrapperDelegate> _origDelegate;
    BOOL _attached;
}

@property (nonatomic, retain) KalturaLibXmlWrapper* parser;
@property (nonatomic, assign) id <KalturaXmlParserDelegate> delegate;
@property (nonatomic, retain) NSError* error;

- (void)attachToParser:(KalturaLibXmlWrapper*)aParser withDelegate:(id <KalturaXmlParserDelegate>)aDelegate;
- (void)detach;
- (void)callDelegateAndDetach;
- (void)parsingFailed:(KalturaXmlParserBase*)aParser;
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
 Class KalturaXmlParserSimpleType
 */
@interface KalturaXmlParserSimpleType : KalturaXmlParserBase
{
    NSString* _value;
}
@end

/*
 Class KalturaXmlParserException
 */
@interface KalturaXmlParserException : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    KalturaXmlParserBase* _subParser;
    KalturaXmlParserBase* _excObjParser;
    KalturaException* _targetException;
}

- (id)initWithSubParser:(KalturaXmlParserBase*)aSubParser;

@end

/*
 Class KalturaXmlParserObject
 */
@interface KalturaXmlParserObject : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    KalturaXmlParserBase* _subParser;
    KalturaObjectBase* _targetObj;
    NSString* _lastTagCapitalized;
    BOOL _lastIsObjectType;
    int _lastPropType;     // KalturaFieldType
}

- (id)initWithObject:(KalturaObjectBase*)aObject;

@end

/*
 Class KalturaXmlParserArray
 */
@interface KalturaXmlParserArray : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    KalturaXmlParserBase* _subParser;
    NSMutableArray* _targetArr;
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

- (id)initWithSubParser:(KalturaXmlParserBase*)aSubParser withPath:(NSArray*)aPath;

@end
