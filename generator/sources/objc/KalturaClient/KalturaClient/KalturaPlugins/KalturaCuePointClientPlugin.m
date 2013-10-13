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
#import "KalturaCuePointClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaCuePointStatus
+ (int)READY
{
    return 1;
}
+ (int)DELETED
{
    return 2;
}
@end

@implementation KalturaCuePointOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)START_TIME_ASC
{
    return @"+startTime";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)START_TIME_DESC
{
    return @"-startTime";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaCuePointType
+ (NSString*)AD
{
    return @"adCuePoint.Ad";
}
+ (NSString*)ANNOTATION
{
    return @"annotation.Annotation";
}
+ (NSString*)CODE
{
    return @"codeCuePoint.Code";
}
@end

///////////////////////// classes /////////////////////////
@interface KalturaCuePoint()
@property (nonatomic,copy) NSString* id;
@property (nonatomic,copy) NSString* cuePointType;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,copy) NSString* userId;
@end

@implementation KalturaCuePoint
@synthesize id = _id;
@synthesize cuePointType = _cuePointType;
@synthesize status = _status;
@synthesize entryId = _entryId;
@synthesize partnerId = _partnerId;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize tags = _tags;
@synthesize startTime = _startTime;
@synthesize userId = _userId;
@synthesize partnerData = _partnerData;
@synthesize partnerSortValue = _partnerSortValue;
@synthesize forceStop = _forceStop;
@synthesize thumbOffset = _thumbOffset;
@synthesize systemName = _systemName;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_status = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_startTime = KALTURA_UNDEF_INT;
    self->_partnerSortValue = KALTURA_UNDEF_INT;
    self->_forceStop = KALTURA_UNDEF_INT;
    self->_thumbOffset = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCuePointType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTags
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStartTime
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerSortValue
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfForceStop
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartTimeFromString:(NSString*)aPropVal
{
    self.startTime = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueFromString:(NSString*)aPropVal
{
    self.partnerSortValue = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setForceStopFromString:(NSString*)aPropVal
{
    self.forceStop = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setThumbOffsetFromString:(NSString*)aPropVal
{
    self.thumbOffset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCuePoint"];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"tags" withString:self.tags];
    [aParams addIfDefinedKey:@"startTime" withInt:self.startTime];
    [aParams addIfDefinedKey:@"partnerData" withString:self.partnerData];
    [aParams addIfDefinedKey:@"partnerSortValue" withInt:self.partnerSortValue];
    [aParams addIfDefinedKey:@"forceStop" withInt:self.forceStop];
    [aParams addIfDefinedKey:@"thumbOffset" withInt:self.thumbOffset];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
}

- (void)dealloc
{
    [self->_id release];
    [self->_cuePointType release];
    [self->_entryId release];
    [self->_tags release];
    [self->_userId release];
    [self->_partnerData release];
    [self->_systemName release];
    [super dealloc];
}

@end

@interface KalturaCuePointListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaCuePointListResponse
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
    return @"KalturaCuePoint";
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
        [aParams putKey:@"objectType" withString:@"KalturaCuePointListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaCuePointBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize cuePointTypeEqual = _cuePointTypeEqual;
@synthesize cuePointTypeIn = _cuePointTypeIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize entryIdIn = _entryIdIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize tagsLike = _tagsLike;
@synthesize tagsMultiLikeOr = _tagsMultiLikeOr;
@synthesize tagsMultiLikeAnd = _tagsMultiLikeAnd;
@synthesize startTimeGreaterThanOrEqual = _startTimeGreaterThanOrEqual;
@synthesize startTimeLessThanOrEqual = _startTimeLessThanOrEqual;
@synthesize userIdEqual = _userIdEqual;
@synthesize userIdIn = _userIdIn;
@synthesize partnerSortValueEqual = _partnerSortValueEqual;
@synthesize partnerSortValueIn = _partnerSortValueIn;
@synthesize partnerSortValueGreaterThanOrEqual = _partnerSortValueGreaterThanOrEqual;
@synthesize partnerSortValueLessThanOrEqual = _partnerSortValueLessThanOrEqual;
@synthesize forceStopEqual = _forceStopEqual;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_startTimeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_startTimeLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerSortValueEqual = KALTURA_UNDEF_INT;
    self->_partnerSortValueGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerSortValueLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_forceStopEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCuePointTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCuePointTypeIn
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

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTagsLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagsMultiLikeAnd
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

- (KalturaFieldType)getTypeOfUserIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerSortValueEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerSortValueIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerSortValueGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerSortValueLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfForceStopEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
{
    return KFT_String;
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartTimeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.startTimeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStartTimeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.startTimeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerSortValueLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.partnerSortValueLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setForceStopEqualFromString:(NSString*)aPropVal
{
    self.forceStopEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCuePointBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withString:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"cuePointTypeEqual" withString:self.cuePointTypeEqual];
    [aParams addIfDefinedKey:@"cuePointTypeIn" withString:self.cuePointTypeIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"entryIdIn" withString:self.entryIdIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"tagsLike" withString:self.tagsLike];
    [aParams addIfDefinedKey:@"tagsMultiLikeOr" withString:self.tagsMultiLikeOr];
    [aParams addIfDefinedKey:@"tagsMultiLikeAnd" withString:self.tagsMultiLikeAnd];
    [aParams addIfDefinedKey:@"startTimeGreaterThanOrEqual" withInt:self.startTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"startTimeLessThanOrEqual" withInt:self.startTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"userIdEqual" withString:self.userIdEqual];
    [aParams addIfDefinedKey:@"userIdIn" withString:self.userIdIn];
    [aParams addIfDefinedKey:@"partnerSortValueEqual" withInt:self.partnerSortValueEqual];
    [aParams addIfDefinedKey:@"partnerSortValueIn" withString:self.partnerSortValueIn];
    [aParams addIfDefinedKey:@"partnerSortValueGreaterThanOrEqual" withInt:self.partnerSortValueGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"partnerSortValueLessThanOrEqual" withInt:self.partnerSortValueLessThanOrEqual];
    [aParams addIfDefinedKey:@"forceStopEqual" withInt:self.forceStopEqual];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
}

- (void)dealloc
{
    [self->_idEqual release];
    [self->_idIn release];
    [self->_cuePointTypeEqual release];
    [self->_cuePointTypeIn release];
    [self->_statusIn release];
    [self->_entryIdEqual release];
    [self->_entryIdIn release];
    [self->_tagsLike release];
    [self->_tagsMultiLikeOr release];
    [self->_tagsMultiLikeAnd release];
    [self->_userIdEqual release];
    [self->_userIdIn release];
    [self->_partnerSortValueIn release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [super dealloc];
}

@end

@implementation KalturaCuePointFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCuePointFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaCuePointService
- (KalturaCuePoint*)addWithCuePoint:(KalturaCuePoint*)aCuePoint
{
    [self.client.params addIfDefinedKey:@"cuePoint" withObject:aCuePoint];
    return [self.client queueObjectService:@"cuepoint_cuepoint" withAction:@"add" withExpectedType:@"KalturaCuePoint"];
}

- (KalturaCuePointListResponse*)addFromBulkWithFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueObjectService:@"cuepoint_cuepoint" withAction:@"addFromBulk" withExpectedType:@"KalturaCuePointListResponse"];
}

- (NSString*)serveBulkWithFilter:(KalturaCuePointFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueServeService:@"cuepoint_cuepoint" withAction:@"serveBulk"];
}

- (NSString*)serveBulkWithFilter:(KalturaCuePointFilter*)aFilter
{
    return [self serveBulkWithFilter:aFilter withPager:nil];
}

- (NSString*)serveBulk
{
    return [self serveBulkWithFilter:nil];
}

- (KalturaCuePoint*)getWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    return [self.client queueObjectService:@"cuepoint_cuepoint" withAction:@"get" withExpectedType:@"KalturaCuePoint"];
}

- (KalturaCuePointListResponse*)listWithFilter:(KalturaCuePointFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"cuepoint_cuepoint" withAction:@"list" withExpectedType:@"KalturaCuePointListResponse"];
}

- (KalturaCuePointListResponse*)listWithFilter:(KalturaCuePointFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaCuePointListResponse*)list
{
    return [self listWithFilter:nil];
}

- (int)countWithFilter:(KalturaCuePointFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"cuepoint_cuepoint" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

- (KalturaCuePoint*)updateWithId:(NSString*)aId withCuePoint:(KalturaCuePoint*)aCuePoint
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"cuePoint" withObject:aCuePoint];
    return [self.client queueObjectService:@"cuepoint_cuepoint" withAction:@"update" withExpectedType:@"KalturaCuePoint"];
}

- (void)deleteWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"cuepoint_cuepoint" withAction:@"delete"];
}

@end

@implementation KalturaCuePointClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaCuePointService*)cuePoint
{
    if (self->_cuePoint == nil)
    	self->_cuePoint = [[KalturaCuePointService alloc] initWithClient:self.client];
    return self->_cuePoint;
}

- (void)dealloc
{
    [self->_cuePoint release];
	[super dealloc];
}

@end

