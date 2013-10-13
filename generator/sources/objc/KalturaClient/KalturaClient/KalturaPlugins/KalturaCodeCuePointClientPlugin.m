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
#import "KalturaCodeCuePointClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaCodeCuePointOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DURATION_ASC
{
    return @"+duration";
}
+ (NSString*)END_TIME_ASC
{
    return @"+endTime";
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
+ (NSString*)DURATION_DESC
{
    return @"-duration";
}
+ (NSString*)END_TIME_DESC
{
    return @"-endTime";
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

///////////////////////// classes /////////////////////////
@interface KalturaCodeCuePoint()
@property (nonatomic,assign) int duration;
@end

@implementation KalturaCodeCuePoint
@synthesize code = _code;
@synthesize description = _description;
@synthesize endTime = _endTime;
@synthesize duration = _duration;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_endTime = KALTURA_UNDEF_INT;
    self->_duration = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCode
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEndTime
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDuration
{
    return KFT_Int;
}

- (void)setEndTimeFromString:(NSString*)aPropVal
{
    self.endTime = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationFromString:(NSString*)aPropVal
{
    self.duration = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCodeCuePoint"];
    [aParams addIfDefinedKey:@"code" withString:self.code];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"endTime" withInt:self.endTime];
}

- (void)dealloc
{
    [self->_code release];
    [self->_description release];
    [super dealloc];
}

@end

@implementation KalturaCodeCuePointBaseFilter
@synthesize codeLike = _codeLike;
@synthesize codeMultiLikeOr = _codeMultiLikeOr;
@synthesize codeMultiLikeAnd = _codeMultiLikeAnd;
@synthesize codeEqual = _codeEqual;
@synthesize codeIn = _codeIn;
@synthesize descriptionLike = _descriptionLike;
@synthesize descriptionMultiLikeOr = _descriptionMultiLikeOr;
@synthesize descriptionMultiLikeAnd = _descriptionMultiLikeAnd;
@synthesize endTimeGreaterThanOrEqual = _endTimeGreaterThanOrEqual;
@synthesize endTimeLessThanOrEqual = _endTimeLessThanOrEqual;
@synthesize durationGreaterThanOrEqual = _durationGreaterThanOrEqual;
@synthesize durationLessThanOrEqual = _durationLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_endTimeGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_endTimeLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_durationGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_durationLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCodeLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCodeMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCodeMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCodeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCodeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescriptionLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescriptionMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescriptionMultiLikeAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEndTimeGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEndTimeLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDurationLessThanOrEqual
{
    return KFT_Int;
}

- (void)setEndTimeGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.endTimeGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEndTimeLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.endTimeLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.durationGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDurationLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.durationLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCodeCuePointBaseFilter"];
    [aParams addIfDefinedKey:@"codeLike" withString:self.codeLike];
    [aParams addIfDefinedKey:@"codeMultiLikeOr" withString:self.codeMultiLikeOr];
    [aParams addIfDefinedKey:@"codeMultiLikeAnd" withString:self.codeMultiLikeAnd];
    [aParams addIfDefinedKey:@"codeEqual" withString:self.codeEqual];
    [aParams addIfDefinedKey:@"codeIn" withString:self.codeIn];
    [aParams addIfDefinedKey:@"descriptionLike" withString:self.descriptionLike];
    [aParams addIfDefinedKey:@"descriptionMultiLikeOr" withString:self.descriptionMultiLikeOr];
    [aParams addIfDefinedKey:@"descriptionMultiLikeAnd" withString:self.descriptionMultiLikeAnd];
    [aParams addIfDefinedKey:@"endTimeGreaterThanOrEqual" withInt:self.endTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"endTimeLessThanOrEqual" withInt:self.endTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"durationGreaterThanOrEqual" withInt:self.durationGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"durationLessThanOrEqual" withInt:self.durationLessThanOrEqual];
}

- (void)dealloc
{
    [self->_codeLike release];
    [self->_codeMultiLikeOr release];
    [self->_codeMultiLikeAnd release];
    [self->_codeEqual release];
    [self->_codeIn release];
    [self->_descriptionLike release];
    [self->_descriptionMultiLikeOr release];
    [self->_descriptionMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaCodeCuePointFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCodeCuePointFilter"];
}

@end

///////////////////////// services /////////////////////////
