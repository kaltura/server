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
#import <Foundation/Foundation.h>
#import "ASIHTTPRequestDelegate.h"
#import "ASIProgressDelegate.h"
#import "KalturaXmlParsers.h"

/*
 Constants
 */
#define KALTURA_UNDEF_BOOL      ((BOOL)CHAR_MIN)
#define KALTURA_UNDEF_INT       INT_MIN
#define KALTURA_UNDEF_FLOAT     NAN
#define KALTURA_UNDEF_STRING    (nil)
#define KALTURA_UNDEF_OBJECT    (nil)

#define KALTURA_NULL_BOOL   ((BOOL)CHAR_MAX)
#define KALTURA_NULL_INT    INT_MAX
#define KALTURA_NULL_FLOAT  INFINITY
#define KALTURA_NULL_STRING (@"__null_string__")
#define KALTURA_NULL_OBJECT ([[[KalturaObjectBase alloc] init] autorelease])

extern NSString* const KalturaClientErrorDomain;

typedef enum {
    KalturaClientErrorAPIException = 1,
    KalturaClientErrorInvalidHttpCode = 2,
    KalturaClientErrorUnknownObjectType = 3,
    KalturaClientErrorXmlParsing = 4,
    KalturaClientErrorUnexpectedTagInSimpleType = 5,
    KalturaClientErrorUnexpectedArrayTag = 6,
    KalturaClientErrorUnexpectedMultiReqTag = 7,
    KalturaClientErrorMissingMultiReqItems = 8,
    KalturaClientErrorMissingObjectTypeTag = 9,
    KalturaClientErrorExpectedObjectTypeTag = 10,
    KalturaClientErrorExpectedPropertyTag = 11,
    KalturaClientErrorStartTagInSimpleType = 12,
    KalturaClientErrorEmptyObject = 13,
} KalturaClientErrorType;

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

/*
 Forward declarations
 */
@protocol KalturaXmlParserDelegate;
@class ASIFormDataRequest;
@class KalturaXmlParserBase;
@class KalturaLibXmlWrapper;
@class KalturaParams;
@class KalturaClientBase;

/*
 Class KalturaClientException
 */
@interface KalturaClientException : NSException
@end

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

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper;

@end

/*
 Class KalturaException
 */
@interface KalturaException : KalturaObjectBase

@property (nonatomic, copy) NSString* code;
@property (nonatomic, copy) NSString* message;

- (NSError*)error;

@end

/*
 Class KalturaObjectFactory
 */
@interface KalturaObjectFactory : NSObject

+ (KalturaObjectBase*)createByName:(NSString*)aName withDefaultType:(NSString*)aDefaultType;

@end

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
- (void)appendQueryString:(NSMutableString*)output;

@end

/*
 Protocol KalturaLogger
 */
@protocol KalturaLogger <NSObject>

- (void)logMessage:(NSString*)aMsg;

@end

/*
 Class KalturaNSLogger
 */
@interface KalturaNSLogger: NSObject <KalturaLogger>

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

/*
 Class KalturaClientConfiguration
 */
@interface KalturaClientConfiguration : NSObject

@property (nonatomic, copy) NSString* serviceUrl;
@property (nonatomic, copy) NSString* clientTag;
@property (nonatomic, assign) int partnerId;
@property (nonatomic, assign) int requestTimeout;
@property (nonatomic, retain) id<KalturaLogger> logger;
@property (nonatomic, copy) NSDictionary* requestHeaders;

@end

/*
 Protocol KalturaClientDelegate
 */
@protocol KalturaClientDelegate

- (void)requestFinished:(KalturaClientBase*)aClient withResult:(id)result;
- (void)requestFailed:(KalturaClientBase*)aClient;

@end

/*
 Class KalturaClientBase
 */
@interface KalturaClientBase : NSObject <ASIHTTPRequestDelegate, KalturaXmlParserDelegate>
{
    BOOL _isMultiRequest;
    KalturaXmlParserBase* _reqParser;
    KalturaXmlParserBase* _skipParser;
    ASIFormDataRequest *_request;
    KalturaLibXmlWrapper* _xmlParser;
    NSDate* _apiStartTime;
}

@property (nonatomic, retain) KalturaClientConfiguration* config;
@property (nonatomic, retain) NSError* error;
@property (nonatomic, assign) id<KalturaClientDelegate> delegate;
@property (nonatomic, assign) id<ASIProgressDelegate> uploadProgressDelegate;
@property (nonatomic, assign) id<ASIProgressDelegate> downloadProgressDelegate;
@property (nonatomic, copy) NSString* ks;
@property (nonatomic, copy) NSString* apiVersion;
@property (nonatomic, readonly) KalturaParams* params;
@property (nonatomic, readonly) NSDictionary* responseHeaders;

    // public messages
- (id)initWithConfig:(KalturaClientConfiguration*)aConfig;
- (void)startMultiRequest;
- (NSArray*)doMultiRequest;
- (void)cancelRequest;
+ (NSString*)generateSessionWithSecret:(NSString*)aSecret withUserId:(NSString*)aUserId withType:(int)aType withPartnerId:(int)aPartnerId withExpiry:(int)aExpiry withPrivileges:(NSString*)aPrivileges;

    // messages for use of auto-gen service code
- (NSString*)queueServeService:(NSString*)aService withAction:(NSString*)aAction;
- (void)queueVoidService:(NSString*)aService withAction:(NSString*)aAction;
- (BOOL)queueBoolService:(NSString*)aService withAction:(NSString*)aAction;
- (int)queueIntService:(NSString*)aService withAction:(NSString*)aAction;
- (double)queueFloatService:(NSString*)aService withAction:(NSString*)aAction;
- (NSString*)queueStringService:(NSString*)aService withAction:(NSString*)aAction;
- (id)queueObjectService:(NSString*)aService withAction:(NSString*)aAction withExpectedType:(NSString*)aExpectedType;
- (NSMutableArray*)queueArrayService:(NSString*)aService withAction:(NSString*)aAction withExpectedType:(NSString*)aExpectedType;

@end
