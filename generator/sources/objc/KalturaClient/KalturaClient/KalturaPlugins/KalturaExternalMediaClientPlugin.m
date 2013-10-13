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
#import "KalturaExternalMediaClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaExternalMediaEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MEDIA_TYPE_ASC
{
    return @"+mediaType";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)MS_DURATION_ASC
{
    return @"+msDuration";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)PLAYS_ASC
{
    return @"+plays";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VIEWS_ASC
{
    return @"+views";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MEDIA_TYPE_DESC
{
    return @"-mediaType";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)MS_DURATION_DESC
{
    return @"-msDuration";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)PLAYS_DESC
{
    return @"-plays";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VIEWS_DESC
{
    return @"-views";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaExternalMediaSourceType
+ (NSString*)INTERCALL
{
    return @"InterCall";
}
+ (NSString*)YOUTUBE
{
    return @"YouTube";
}
@end

///////////////////////// classes /////////////////////////
@interface KalturaExternalMediaEntry()
@property (nonatomic,copy) NSString* assetParamsIds;
@end

@implementation KalturaExternalMediaEntry
@synthesize externalSourceType = _externalSourceType;
@synthesize assetParamsIds = _assetParamsIds;

- (KalturaFieldType)getTypeOfExternalSourceType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetParamsIds
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaExternalMediaEntry"];
    [aParams addIfDefinedKey:@"externalSourceType" withString:self.externalSourceType];
}

- (void)dealloc
{
    [self->_externalSourceType release];
    [self->_assetParamsIds release];
    [super dealloc];
}

@end

@interface KalturaExternalMediaEntryListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaExternalMediaEntryListResponse
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
    return @"KalturaExternalMediaEntry";
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
        [aParams putKey:@"objectType" withString:@"KalturaExternalMediaEntryListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaExternalMediaEntryBaseFilter
@synthesize externalSourceTypeEqual = _externalSourceTypeEqual;
@synthesize externalSourceTypeIn = _externalSourceTypeIn;
@synthesize assetParamsIdsMatchOr = _assetParamsIdsMatchOr;
@synthesize assetParamsIdsMatchAnd = _assetParamsIdsMatchAnd;

- (KalturaFieldType)getTypeOfExternalSourceTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfExternalSourceTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetParamsIdsMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetParamsIdsMatchAnd
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaExternalMediaEntryBaseFilter"];
    [aParams addIfDefinedKey:@"externalSourceTypeEqual" withString:self.externalSourceTypeEqual];
    [aParams addIfDefinedKey:@"externalSourceTypeIn" withString:self.externalSourceTypeIn];
    [aParams addIfDefinedKey:@"assetParamsIdsMatchOr" withString:self.assetParamsIdsMatchOr];
    [aParams addIfDefinedKey:@"assetParamsIdsMatchAnd" withString:self.assetParamsIdsMatchAnd];
}

- (void)dealloc
{
    [self->_externalSourceTypeEqual release];
    [self->_externalSourceTypeIn release];
    [self->_assetParamsIdsMatchOr release];
    [self->_assetParamsIdsMatchAnd release];
    [super dealloc];
}

@end

@implementation KalturaExternalMediaEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaExternalMediaEntryFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaExternalMediaService
- (KalturaExternalMediaEntry*)addWithEntry:(KalturaExternalMediaEntry*)aEntry
{
    [self.client.params addIfDefinedKey:@"entry" withObject:aEntry];
    return [self.client queueObjectService:@"externalmedia_externalmedia" withAction:@"add" withExpectedType:@"KalturaExternalMediaEntry"];
}

- (KalturaExternalMediaEntry*)getWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"externalmedia_externalmedia" withAction:@"get" withExpectedType:@"KalturaExternalMediaEntry"];
}

- (KalturaExternalMediaEntry*)updateWithId:(NSString*)aId withEntry:(KalturaExternalMediaEntry*)aEntry
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"entry" withObject:aEntry];
    return [self.client queueObjectService:@"externalmedia_externalmedia" withAction:@"update" withExpectedType:@"KalturaExternalMediaEntry"];
}

- (void)deleteWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"externalmedia_externalmedia" withAction:@"delete"];
}

- (KalturaExternalMediaEntryListResponse*)listWithFilter:(KalturaExternalMediaEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"externalmedia_externalmedia" withAction:@"list" withExpectedType:@"KalturaExternalMediaEntryListResponse"];
}

- (KalturaExternalMediaEntryListResponse*)listWithFilter:(KalturaExternalMediaEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaExternalMediaEntryListResponse*)list
{
    return [self listWithFilter:nil];
}

- (int)countWithFilter:(KalturaExternalMediaEntryFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"externalmedia_externalmedia" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

@end

@implementation KalturaExternalMediaClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaExternalMediaService*)externalMedia
{
    if (self->_externalMedia == nil)
    	self->_externalMedia = [[KalturaExternalMediaService alloc] initWithClient:self.client];
    return self->_externalMedia;
}

- (void)dealloc
{
    [self->_externalMedia release];
	[super dealloc];
}

@end

