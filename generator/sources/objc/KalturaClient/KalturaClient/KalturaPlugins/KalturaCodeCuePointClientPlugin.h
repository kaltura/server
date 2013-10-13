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
@interface KalturaCodeCuePointOrderBy : NSObject
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

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaCodeCuePoint : KalturaCuePoint
@property (nonatomic,copy) NSString* code;
@property (nonatomic,copy) NSString* description;
@property (nonatomic,assign) int endTime;
// Duration in milliseconds
@property (nonatomic,assign,readonly) int duration;
- (KalturaFieldType)getTypeOfCode;
- (KalturaFieldType)getTypeOfDescription;
- (KalturaFieldType)getTypeOfEndTime;
- (KalturaFieldType)getTypeOfDuration;
- (void)setEndTimeFromString:(NSString*)aPropVal;
- (void)setDurationFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCodeCuePointBaseFilter : KalturaCuePointFilter
@property (nonatomic,copy) NSString* codeLike;
@property (nonatomic,copy) NSString* codeMultiLikeOr;
@property (nonatomic,copy) NSString* codeMultiLikeAnd;
@property (nonatomic,copy) NSString* codeEqual;
@property (nonatomic,copy) NSString* codeIn;
@property (nonatomic,copy) NSString* descriptionLike;
@property (nonatomic,copy) NSString* descriptionMultiLikeOr;
@property (nonatomic,copy) NSString* descriptionMultiLikeAnd;
@property (nonatomic,assign) int endTimeGreaterThanOrEqual;
@property (nonatomic,assign) int endTimeLessThanOrEqual;
@property (nonatomic,assign) int durationGreaterThanOrEqual;
@property (nonatomic,assign) int durationLessThanOrEqual;
- (KalturaFieldType)getTypeOfCodeLike;
- (KalturaFieldType)getTypeOfCodeMultiLikeOr;
- (KalturaFieldType)getTypeOfCodeMultiLikeAnd;
- (KalturaFieldType)getTypeOfCodeEqual;
- (KalturaFieldType)getTypeOfCodeIn;
- (KalturaFieldType)getTypeOfDescriptionLike;
- (KalturaFieldType)getTypeOfDescriptionMultiLikeOr;
- (KalturaFieldType)getTypeOfDescriptionMultiLikeAnd;
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
@interface KalturaCodeCuePointFilter : KalturaCodeCuePointBaseFilter
@end

///////////////////////// services /////////////////////////
