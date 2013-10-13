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
#import "KalturaCuePointClientPlugin.h"

///////////////////////// enums /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaAdCuePointOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)DURATION_ASC;
+ (NSString*)END_TIME_ASC;
+ (NSString*)PARTNER_SORT_VALUE_ASC;
+ (NSString*)START_TIME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)DURATION_DESC;
+ (NSString*)END_TIME_DESC;
+ (NSString*)PARTNER_SORT_VALUE_DESC;
+ (NSString*)START_TIME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaAdProtocolType : NSObject
+ (NSString*)CUSTOM;
+ (NSString*)VAST;
+ (NSString*)VAST_2_0;
+ (NSString*)VPAID;
@end

// @package External
// @subpackage Kaltura
@interface KalturaAdType : NSObject
+ (NSString*)VIDEO;
+ (NSString*)OVERLAY;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaAdCuePoint : KalturaCuePoint
@property (nonatomic,copy) NSString* protocolType;	// enum KalturaAdProtocolType, insertonly
@property (nonatomic,copy) NSString* sourceUrl;
@property (nonatomic,copy) NSString* adType;	// enum KalturaAdType
@property (nonatomic,copy) NSString* title;
@property (nonatomic,assign) int endTime;
// Duration in milliseconds
@property (nonatomic,assign,readonly) int duration;
- (KalturaFieldType)getTypeOfProtocolType;
- (KalturaFieldType)getTypeOfSourceUrl;
- (KalturaFieldType)getTypeOfAdType;
- (KalturaFieldType)getTypeOfTitle;
- (KalturaFieldType)getTypeOfEndTime;
- (KalturaFieldType)getTypeOfDuration;
- (void)setEndTimeFromString:(NSString*)aPropVal;
- (void)setDurationFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaAdCuePointBaseFilter : KalturaCuePointFilter
@property (nonatomic,copy) NSString* protocolTypeEqual;	// enum KalturaAdProtocolType
@property (nonatomic,copy) NSString* protocolTypeIn;
@property (nonatomic,copy) NSString* titleLike;
@property (nonatomic,copy) NSString* titleMultiLikeOr;
@property (nonatomic,copy) NSString* titleMultiLikeAnd;
@property (nonatomic,assign) int endTimeGreaterThanOrEqual;
@property (nonatomic,assign) int endTimeLessThanOrEqual;
@property (nonatomic,assign) int durationGreaterThanOrEqual;
@property (nonatomic,assign) int durationLessThanOrEqual;
- (KalturaFieldType)getTypeOfProtocolTypeEqual;
- (KalturaFieldType)getTypeOfProtocolTypeIn;
- (KalturaFieldType)getTypeOfTitleLike;
- (KalturaFieldType)getTypeOfTitleMultiLikeOr;
- (KalturaFieldType)getTypeOfTitleMultiLikeAnd;
- (KalturaFieldType)getTypeOfEndTimeGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfEndTimeLessThanOrEqual;
- (KalturaFieldType)getTypeOfDurationGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfDurationLessThanOrEqual;
- (void)setEndTimeGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setEndTimeLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setDurationGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setDurationLessThanOrEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaAdCuePointFilter : KalturaAdCuePointBaseFilter
@end

///////////////////////// services /////////////////////////
