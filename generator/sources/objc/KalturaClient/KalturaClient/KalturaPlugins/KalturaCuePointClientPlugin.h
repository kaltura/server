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
// @package External
// @subpackage Kaltura
#import "../KalturaClient.h"

///////////////////////// enums /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaCuePointStatus : NSObject
+ (int)READY;
+ (int)DELETED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCuePointOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)PARTNER_SORT_VALUE_ASC;
+ (NSString*)START_TIME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)PARTNER_SORT_VALUE_DESC;
+ (NSString*)START_TIME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCuePointType : NSObject
+ (NSString*)AD;
+ (NSString*)ANNOTATION;
+ (NSString*)CODE;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaCuePoint : KalturaObjectBase
@property (nonatomic,copy,readonly) NSString* id;
@property (nonatomic,copy,readonly) NSString* cuePointType;	// enum KalturaCuePointType
@property (nonatomic,assign,readonly) int status;	// enum KalturaCuePointStatus
@property (nonatomic,copy) NSString* entryId;	// insertonly
@property (nonatomic,assign,readonly) int partnerId;
@property (nonatomic,assign,readonly) int createdAt;
@property (nonatomic,assign,readonly) int updatedAt;
@property (nonatomic,copy) NSString* tags;
// Start time in milliseconds
@property (nonatomic,assign) int startTime;
@property (nonatomic,copy,readonly) NSString* userId;
@property (nonatomic,copy) NSString* partnerData;
@property (nonatomic,assign) int partnerSortValue;
@property (nonatomic,assign) int forceStop;	// enum KalturaNullableBoolean
@property (nonatomic,assign) int thumbOffset;
@property (nonatomic,copy) NSString* systemName;
- (KalturaFieldType)getTypeOfId;
- (KalturaFieldType)getTypeOfCuePointType;
- (KalturaFieldType)getTypeOfStatus;
- (KalturaFieldType)getTypeOfEntryId;
- (KalturaFieldType)getTypeOfPartnerId;
- (KalturaFieldType)getTypeOfCreatedAt;
- (KalturaFieldType)getTypeOfUpdatedAt;
- (KalturaFieldType)getTypeOfTags;
- (KalturaFieldType)getTypeOfStartTime;
- (KalturaFieldType)getTypeOfUserId;
- (KalturaFieldType)getTypeOfPartnerData;
- (KalturaFieldType)getTypeOfPartnerSortValue;
- (KalturaFieldType)getTypeOfForceStop;
- (KalturaFieldType)getTypeOfThumbOffset;
- (KalturaFieldType)getTypeOfSystemName;
- (void)setStatusFromString:(NSString*)aPropVal;
- (void)setPartnerIdFromString:(NSString*)aPropVal;
- (void)setCreatedAtFromString:(NSString*)aPropVal;
- (void)setUpdatedAtFromString:(NSString*)aPropVal;
- (void)setStartTimeFromString:(NSString*)aPropVal;
- (void)setPartnerSortValueFromString:(NSString*)aPropVal;
- (void)setForceStopFromString:(NSString*)aPropVal;
- (void)setThumbOffsetFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCuePointListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaCuePoint elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCuePointBaseFilter : KalturaFilter
@property (nonatomic,copy) NSString* idEqual;
@property (nonatomic,copy) NSString* idIn;
@property (nonatomic,copy) NSString* cuePointTypeEqual;	// enum KalturaCuePointType
@property (nonatomic,copy) NSString* cuePointTypeIn;
@property (nonatomic,assign) int statusEqual;	// enum KalturaCuePointStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,copy) NSString* entryIdEqual;
@property (nonatomic,copy) NSString* entryIdIn;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
@property (nonatomic,copy) NSString* tagsLike;
@property (nonatomic,copy) NSString* tagsMultiLikeOr;
@property (nonatomic,copy) NSString* tagsMultiLikeAnd;
@property (nonatomic,assign) int startTimeGreaterThanOrEqual;
@property (nonatomic,assign) int startTimeLessThanOrEqual;
@property (nonatomic,copy) NSString* userIdEqual;
@property (nonatomic,copy) NSString* userIdIn;
@property (nonatomic,assign) int partnerSortValueEqual;
@property (nonatomic,copy) NSString* partnerSortValueIn;
@property (nonatomic,assign) int partnerSortValueGreaterThanOrEqual;
@property (nonatomic,assign) int partnerSortValueLessThanOrEqual;
@property (nonatomic,assign) int forceStopEqual;	// enum KalturaNullableBoolean
@property (nonatomic,copy) NSString* systemNameEqual;
@property (nonatomic,copy) NSString* systemNameIn;
- (KalturaFieldType)getTypeOfIdEqual;
- (KalturaFieldType)getTypeOfIdIn;
- (KalturaFieldType)getTypeOfCuePointTypeEqual;
- (KalturaFieldType)getTypeOfCuePointTypeIn;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfEntryIdEqual;
- (KalturaFieldType)getTypeOfEntryIdIn;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfTagsLike;
- (KalturaFieldType)getTypeOfTagsMultiLikeOr;
- (KalturaFieldType)getTypeOfTagsMultiLikeAnd;
- (KalturaFieldType)getTypeOfStartTimeGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfStartTimeLessThanOrEqual;
- (KalturaFieldType)getTypeOfUserIdEqual;
- (KalturaFieldType)getTypeOfUserIdIn;
- (KalturaFieldType)getTypeOfPartnerSortValueEqual;
- (KalturaFieldType)getTypeOfPartnerSortValueIn;
- (KalturaFieldType)getTypeOfPartnerSortValueGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfPartnerSortValueLessThanOrEqual;
- (KalturaFieldType)getTypeOfForceStopEqual;
- (KalturaFieldType)getTypeOfSystemNameEqual;
- (KalturaFieldType)getTypeOfSystemNameIn;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setStartTimeGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setStartTimeLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setPartnerSortValueEqualFromString:(NSString*)aPropVal;
- (void)setPartnerSortValueGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setPartnerSortValueLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setForceStopEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCuePointFilter : KalturaCuePointBaseFilter
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// Cue Point service
@interface KalturaCuePointService : KalturaServiceBase
// Allows you to add an cue point object associated with an entry
- (KalturaCuePoint*)addWithCuePoint:(KalturaCuePoint*)aCuePoint;
// Allows you to add multiple cue points objects by uploading XML that contains multiple cue point definitions
- (KalturaCuePointListResponse*)addFromBulkWithFileData:(NSString*)aFileData;
// Download multiple cue points objects as XML definitions
- (NSString*)serveBulkWithFilter:(KalturaCuePointFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (NSString*)serveBulkWithFilter:(KalturaCuePointFilter*)aFilter;
- (NSString*)serveBulk;
// Retrieve an CuePoint object by id
- (KalturaCuePoint*)getWithId:(NSString*)aId;
// List cue point objects by filter and pager
- (KalturaCuePointListResponse*)listWithFilter:(KalturaCuePointFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaCuePointListResponse*)listWithFilter:(KalturaCuePointFilter*)aFilter;
- (KalturaCuePointListResponse*)list;
// count cue point objects by filter
- (int)countWithFilter:(KalturaCuePointFilter*)aFilter;
- (int)count;
// Update cue point by id
- (KalturaCuePoint*)updateWithId:(NSString*)aId withCuePoint:(KalturaCuePoint*)aCuePoint;
// delete cue point by id, and delete all children cue points
- (void)deleteWithId:(NSString*)aId;
@end

@interface KalturaCuePointClientPlugin : KalturaClientPlugin
{
	KalturaCuePointService* _cuePoint;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaCuePointService* cuePoint;
@end

