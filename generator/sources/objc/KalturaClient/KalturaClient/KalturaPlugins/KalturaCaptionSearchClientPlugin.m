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
#import "KalturaCaptionSearchClientPlugin.h"

///////////////////////// enums /////////////////////////
///////////////////////// classes /////////////////////////
@implementation KalturaCaptionAssetItem
@synthesize asset = _asset;
@synthesize entry = _entry;
@synthesize startTime = _startTime;
@synthesize endTime = _endTime;
@synthesize content = _content;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_startTime = KALTURA_UNDEF_INT;
    self->_endTime = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfAsset
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfAsset
{
    return @"KalturaCaptionAsset";
}

- (KalturaFieldType)getTypeOfEntry
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfEntry
{
    return @"KalturaBaseEntry";
}

- (KalturaFieldType)getTypeOfStartTime
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndTime
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfContent
{
    return KFT_String;
}

- (void)setStartTimeFromString:(NSString*)aPropVal
{
    self.startTime = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndTimeFromString:(NSString*)aPropVal
{
    self.endTime = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAssetItem"];
    [aParams addIfDefinedKey:@"asset" withObject:self.asset];
    [aParams addIfDefinedKey:@"entry" withObject:self.entry];
    [aParams addIfDefinedKey:@"startTime" withInt:self.startTime];
    [aParams addIfDefinedKey:@"endTime" withInt:self.endTime];
    [aParams addIfDefinedKey:@"content" withString:self.content];
}

- (void)dealloc
{
    [self->_asset release];
    [self->_entry release];
    [self->_content release];
    [super dealloc];
}

@end

@interface KalturaCaptionAssetItemListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCaptionAssetItemListResponse
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
    return @"KalturaCaptionAssetItem";
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
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAssetItemListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaCaptionAssetItemFilter
@synthesize contentLike = _contentLike;
@synthesize contentMultiLikeOr = _contentMultiLikeOr;
@synthesize contentMultiLikeAnd = _contentMultiLikeAnd;
@synthesize partnerDescriptionLike = _partnerDescriptionLike;
@synthesize partnerDescriptionMultiLikeOr = _partnerDescriptionMultiLikeOr;
@synthesize partnerDescriptionMultiLikeAnd = _partnerDescriptionMultiLikeAnd;
@synthesize languageEqual = _languageEqual;
@synthesize languageIn = _languageIn;
@synthesize labelEqual = _labelEqual;
@synthesize labelIn = _labelIn;
@synthesize startTimeGreaterThanOrEqual = _startTimeGreaterThanOrEqual;
@synthesize startTimeLessThanOrEqual = _startTimeLessThanOrEqual;
@synthesize endTimeGreaterThanOrEqual = _endTimeGreaterThanOrEqual;
@synthesize endTimeLessThanOrEqual = _endTimeLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_startTimeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_startTimeLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_endTimeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_endTimeLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfContentLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContentMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContentMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerDescriptionLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerDescriptionMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerDescriptionMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLanguageEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLanguageIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLabelEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLabelIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStartTimeGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStartTimeLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndTimeGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndTimeLessThanOrEqual
{
    return KFT_Int;
}

- (void)setStartTimeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.startTimeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartTimeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.startTimeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndTimeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.endTimeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndTimeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.endTimeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCaptionAssetItemFilter"];
    [aParams addIfDefinedKey:@"contentLike" withString:self.contentLike];
    [aParams addIfDefinedKey:@"contentMultiLikeOr" withString:self.contentMultiLikeOr];
    [aParams addIfDefinedKey:@"contentMultiLikeAnd" withString:self.contentMultiLikeAnd];
    [aParams addIfDefinedKey:@"partnerDescriptionLike" withString:self.partnerDescriptionLike];
    [aParams addIfDefinedKey:@"partnerDescriptionMultiLikeOr" withString:self.partnerDescriptionMultiLikeOr];
    [aParams addIfDefinedKey:@"partnerDescriptionMultiLikeAnd" withString:self.partnerDescriptionMultiLikeAnd];
    [aParams addIfDefinedKey:@"languageEqual" withString:self.languageEqual];
    [aParams addIfDefinedKey:@"languageIn" withString:self.languageIn];
    [aParams addIfDefinedKey:@"labelEqual" withString:self.labelEqual];
    [aParams addIfDefinedKey:@"labelIn" withString:self.labelIn];
    [aParams addIfDefinedKey:@"startTimeGreaterThanOrEqual" withInt:self.startTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"startTimeLessThanOrEqual" withInt:self.startTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"endTimeGreaterThanOrEqual" withInt:self.endTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"endTimeLessThanOrEqual" withInt:self.endTimeLessThanOrEqual];
}

- (void)dealloc
{
    [self->_contentLike release];
    [self->_contentMultiLikeOr release];
    [self->_contentMultiLikeAnd release];
    [self->_partnerDescriptionLike release];
    [self->_partnerDescriptionMultiLikeOr release];
    [self->_partnerDescriptionMultiLikeAnd release];
    [self->_languageEqual release];
    [self->_languageIn release];
    [self->_labelEqual release];
    [self->_labelIn release];
    [super dealloc];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaCaptionAssetItemService
- (KalturaCaptionAssetItemListResponse*)searchWithEntryFilter:(KalturaBaseEntryFilter*)aEntryFilter withCaptionAssetItemFilter:(KalturaCaptionAssetItemFilter*)aCaptionAssetItemFilter withCaptionAssetItemPager:(KalturaFilterPager*)aCaptionAssetItemPager
{
    [self.client.params addIfDefinedKey:@"entryFilter" withObject:aEntryFilter];
    [self.client.params addIfDefinedKey:@"captionAssetItemFilter" withObject:aCaptionAssetItemFilter];
    [self.client.params addIfDefinedKey:@"captionAssetItemPager" withObject:aCaptionAssetItemPager];
    return [self.client queueObjectService:@"captionsearch_captionassetitem" withAction:@"search" withExpectedType:@"KalturaCaptionAssetItemListResponse"];
}

- (KalturaCaptionAssetItemListResponse*)searchWithEntryFilter:(KalturaBaseEntryFilter*)aEntryFilter withCaptionAssetItemFilter:(KalturaCaptionAssetItemFilter*)aCaptionAssetItemFilter
{
    return [self searchWithEntryFilter:aEntryFilter withCaptionAssetItemFilter:aCaptionAssetItemFilter withCaptionAssetItemPager:nil];
}

- (KalturaCaptionAssetItemListResponse*)searchWithEntryFilter:(KalturaBaseEntryFilter*)aEntryFilter
{
    return [self searchWithEntryFilter:aEntryFilter withCaptionAssetItemFilter:nil];
}

- (KalturaCaptionAssetItemListResponse*)search
{
    return [self searchWithEntryFilter:nil];
}

@end

@implementation KalturaCaptionSearchClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaCaptionAssetItemService*)captionAssetItem
{
    if (self->_captionAssetItem == nil)
    	self->_captionAssetItem = [[KalturaCaptionAssetItemService alloc] initWithClient:self.client];
    return self->_captionAssetItem;
}

- (void)dealloc
{
    [self->_captionAssetItem release];
	[super dealloc];
}

@end

