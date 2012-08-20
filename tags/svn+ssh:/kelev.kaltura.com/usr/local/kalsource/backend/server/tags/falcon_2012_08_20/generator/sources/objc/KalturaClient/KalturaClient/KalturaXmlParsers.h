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
    NSString* _expectedType;
    NSString* _lastTagCapitalized;
    BOOL _lastIsObjectType;
    int _lastPropType;     // KalturaFieldType
}

- (id)initWithObject:(KalturaObjectBase*)aObject;
- (id)initWithExpectedType:(NSString*)aExpectedType;

@end

/*
 Class KalturaXmlParserArray
 */
@interface KalturaXmlParserArray : KalturaXmlParserBase <KalturaXmlParserDelegate>
{
    KalturaXmlParserBase* _subParser;
    NSString* _expectedType;
    NSMutableArray* _targetArr;
}

- (id)initWithExpectedType:(NSString*)aExpectedType;

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
