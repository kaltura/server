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
#import "KalturaCaptionClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaCaptionAssetStatus
+ (int)ERROR
{
    return -1;
}
+ (int)QUEUED
{
    return 0;
}
+ (int)READY
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)IMPORTING
{
    return 7;
}
+ (int)EXPORTING
{
    return 9;
}
@end

@implementation KalturaCaptionAssetOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DELETED_AT_ASC
{
    return @"+deletedAt";
}
+ (NSString*)SIZE_ASC
{
    return @"+size";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DELETED_AT_DESC
{
    return @"-deletedAt";
}
+ (NSString*)SIZE_DESC
{
    return @"-size";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaCaptionParamsOrderBy
@end

@implementation KalturaCaptionType
+ (NSString*)SRT
{
    return @"1";
}
+ (NSString*)DFXP
{
    return @"2";
}
+ (NSString*)WEBVTT
{
    return @"3";
}
@end

///////////////////////// classes /////////////////////////
@interface KalturaCaptionAsset()
@property (nonatomic,copy) NSString* languageCode;
@property (nonatomic,assign) int status;
@end

@implementation KalturaCaptionAsset
@synthesize captionParamsId = _captionParamsId;
@synthesize language = _language;
@synthesize languageCode = _languageCode;
@synthesize isDefault = _isDefault;
@synthesize label = _label;
@synthesize format = _format;
@synthesize status = _status;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_captionParamsId = KALTURA_UNDEF_INT;
    self->_isDefault = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCaptionParamsId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLanguage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLanguageCode
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLabel
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (void)setCaptionParamsIdFromString:(NSString*)aPropVal
{
    self.captionParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAsset"];
    [aParams addIfDefinedKey:@"captionParamsId" withInt:self.captionParamsId];
    [aParams addIfDefinedKey:@"language" withString:self.language];
    [aParams addIfDefinedKey:@"isDefault" withInt:self.isDefault];
    [aParams addIfDefinedKey:@"label" withString:self.label];
    [aParams addIfDefinedKey:@"format" withString:self.format];
}

- (void)dealloc
{
    [self->_language release];
    [self->_languageCode release];
    [self->_label release];
    [self->_format release];
    [super dealloc];
}

@end

@interface KalturaCaptionAssetListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCaptionAssetListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaCaptionAsset";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAssetListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaCaptionParams
@synthesize language = _language;
@synthesize isDefault = _isDefault;
@synthesize label = _label;
@synthesize format = _format;
@synthesize sourceParamsId = _sourceParamsId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_isDefault = KALTURA_UNDEF_INT;
    self->_sourceParamsId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfLanguage
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfLabel
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceParamsId
{
    return KFT_Int;
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSourceParamsIdFromString:(NSString*)aPropVal
{
    self.sourceParamsId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionParams"];
    [aParams addIfDefinedKey:@"language" withString:self.language];
    [aParams addIfDefinedKey:@"isDefault" withInt:self.isDefault];
    [aParams addIfDefinedKey:@"label" withString:self.label];
    [aParams addIfDefinedKey:@"format" withString:self.format];
    [aParams addIfDefinedKey:@"sourceParamsId" withInt:self.sourceParamsId];
}

- (void)dealloc
{
    [self->_language release];
    [self->_label release];
    [self->_format release];
    [super dealloc];
}

@end

@interface KalturaCaptionParamsListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCaptionParamsListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaCaptionParams";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionParamsListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaCaptionAssetBaseFilter
@synthesize captionParamsIdEqual = _captionParamsIdEqual;
@synthesize captionParamsIdIn = _captionParamsIdIn;
@synthesize formatEqual = _formatEqual;
@synthesize formatIn = _formatIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize statusNotIn = _statusNotIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_captionParamsIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCaptionParamsIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCaptionParamsIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFormatEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFormatIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusNotIn
{
    return KFT_String;
}

- (void)setCaptionParamsIdEqualFromString:(NSString*)aPropVal
{
    self.captionParamsIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAssetBaseFilter"];
    [aParams addIfDefinedKey:@"captionParamsIdEqual" withInt:self.captionParamsIdEqual];
    [aParams addIfDefinedKey:@"captionParamsIdIn" withString:self.captionParamsIdIn];
    [aParams addIfDefinedKey:@"formatEqual" withString:self.formatEqual];
    [aParams addIfDefinedKey:@"formatIn" withString:self.formatIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"statusNotIn" withString:self.statusNotIn];
}

- (void)dealloc
{
    [self->_captionParamsIdIn release];
    [self->_formatEqual release];
    [self->_formatIn release];
    [self->_statusIn release];
    [self->_statusNotIn release];
    [super dealloc];
}

@end

@implementation KalturaCaptionParamsBaseFilter
@synthesize formatEqual = _formatEqual;
@synthesize formatIn = _formatIn;

- (KalturaFieldType)getTypeOfFormatEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFormatIn
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionParamsBaseFilter"];
    [aParams addIfDefinedKey:@"formatEqual" withString:self.formatEqual];
    [aParams addIfDefinedKey:@"formatIn" withString:self.formatIn];
}

- (void)dealloc
{
    [self->_formatEqual release];
    [self->_formatIn release];
    [super dealloc];
}

@end

@implementation KalturaCaptionAssetFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAssetFilter"];
}

@end

@implementation KalturaCaptionParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionParamsFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaCaptionAssetService
- (KalturaCaptionAsset*)addWithEntryId:(NSString*)aEntryId withCaptionAsset:(KalturaCaptionAsset*)aCaptionAsset
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"captionAsset" withObject:aCaptionAsset];
    return [self.client queueObjectService:@"caption_captionasset" withAction:@"add" withExpectedType:@"KalturaCaptionAsset"];
}

- (KalturaCaptionAsset*)setContentWithId:(NSString*)aId withContentResource:(KalturaContentResource*)aContentResource
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"contentResource" withObject:aContentResource];
    return [self.client queueObjectService:@"caption_captionasset" withAction:@"setContent" withExpectedType:@"KalturaCaptionAsset"];
}

- (KalturaCaptionAsset*)updateWithId:(NSString*)aId withCaptionAsset:(KalturaCaptionAsset*)aCaptionAsset
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"captionAsset" withObject:aCaptionAsset];
    return [self.client queueObjectService:@"caption_captionasset" withAction:@"update" withExpectedType:@"KalturaCaptionAsset"];
}

- (NSString*)serveByEntryIdWithEntryId:(NSString*)aEntryId withCaptionParamId:(int)aCaptionParamId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"captionParamId" withInt:aCaptionParamId];
    return [self.client queueServeService:@"caption_captionasset" withAction:@"serveByEntryId"];
}

- (NSString*)serveByEntryIdWithEntryId:(NSString*)aEntryId
{
    return [self serveByEntryIdWithEntryId:aEntryId withCaptionParamId:KALTURA_UNDEF_INT];
}

- (NSString*)getUrlWithId:(NSString*)aId withStorageId:(int)aStorageId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"storageId" withInt:aStorageId];
    return [self.client queueStringService:@"caption_captionasset" withAction:@"getUrl"];
}

- (NSString*)getUrlWithId:(NSString*)aId
{
    return [self getUrlWithId:aId withStorageId:KALTURA_UNDEF_INT];
}

- (KalturaRemotePathListResponse*)getRemotePathsWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"caption_captionasset" withAction:@"getRemotePaths" withExpectedType:@"KalturaRemotePathListResponse"];
}

- (NSString*)serveWithCaptionAssetId:(NSString*)aCaptionAssetId
{
    [self.client.params addIfDefinedKey:@"captionAssetId" withString:aCaptionAssetId];
    return [self.client queueServeService:@"caption_captionasset" withAction:@"serve"];
}

- (void)setAsDefaultWithCaptionAssetId:(NSString*)aCaptionAssetId
{
    [self.client.params addIfDefinedKey:@"captionAssetId" withString:aCaptionAssetId];
    [self.client queueVoidService:@"caption_captionasset" withAction:@"setAsDefault"];
}

- (KalturaCaptionAsset*)getWithCaptionAssetId:(NSString*)aCaptionAssetId
{
    [self.client.params addIfDefinedKey:@"captionAssetId" withString:aCaptionAssetId];
    return [self.client queueObjectService:@"caption_captionasset" withAction:@"get" withExpectedType:@"KalturaCaptionAsset"];
}

- (KalturaCaptionAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"caption_captionasset" withAction:@"list" withExpectedType:@"KalturaCaptionAssetListResponse"];
}

- (KalturaCaptionAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaCaptionAssetListResponse*)list
{
    return [self listWithFilter:nil];
}

- (void)deleteWithCaptionAssetId:(NSString*)aCaptionAssetId
{
    [self.client.params addIfDefinedKey:@"captionAssetId" withString:aCaptionAssetId];
    [self.client queueVoidService:@"caption_captionasset" withAction:@"delete"];
}

@end

@implementation KalturaCaptionParamsService
- (KalturaCaptionParams*)addWithCaptionParams:(KalturaCaptionParams*)aCaptionParams
{
    [self.client.params addIfDefinedKey:@"captionParams" withObject:aCaptionParams];
    return [self.client queueObjectService:@"caption_captionparams" withAction:@"add" withExpectedType:@"KalturaCaptionParams"];
}

- (KalturaCaptionParams*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"caption_captionparams" withAction:@"get" withExpectedType:@"KalturaCaptionParams"];
}

- (KalturaCaptionParams*)updateWithId:(int)aId withCaptionParams:(KalturaCaptionParams*)aCaptionParams
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"captionParams" withObject:aCaptionParams];
    return [self.client queueObjectService:@"caption_captionparams" withAction:@"update" withExpectedType:@"KalturaCaptionParams"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"caption_captionparams" withAction:@"delete"];
}

- (KalturaCaptionParamsListResponse*)listWithFilter:(KalturaCaptionParamsFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"caption_captionparams" withAction:@"list" withExpectedType:@"KalturaCaptionParamsListResponse"];
}

- (KalturaCaptionParamsListResponse*)listWithFilter:(KalturaCaptionParamsFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaCaptionParamsListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaCaptionClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaCaptionAssetService*)captionAsset
{
    if (self->_captionAsset == nil)
    	self->_captionAsset = [[KalturaCaptionAssetService alloc] initWithClient:self.client];
    return self->_captionAsset;
}

- (KalturaCaptionParamsService*)captionParams
{
    if (self->_captionParams == nil)
    	self->_captionParams = [[KalturaCaptionParamsService alloc] initWithClient:self.client];
    return self->_captionParams;
}

- (void)dealloc
{
    [self->_captionAsset release];
    [self->_captionParams release];
	[super dealloc];
}

@end

