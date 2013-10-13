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
#import "KalturaAdCuePointClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaAdCuePointOrderBy
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

@implementation KalturaAdProtocolType
+ (NSString*)CUSTOM
{
    return @"0";
}
+ (NSString*)VAST
{
    return @"1";
}
+ (NSString*)VAST_2_0
{
    return @"2";
}
+ (NSString*)VPAID
{
    return @"3";
}
@end

@implementation KalturaAdType
+ (NSString*)VIDEO
{
    return @"1";
}
+ (NSString*)OVERLAY
{
    return @"2";
}
@end

///////////////////////// classes /////////////////////////
@interface KalturaAdCuePoint()
@property (nonatomic,assign) int duration;
@end

@implementation KalturaAdCuePoint
@synthesize protocolType = _protocolType;
@synthesize sourceUrl = _sourceUrl;
@synthesize adType = _adType;
@synthesize title = _title;
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

- (KalturaFieldType)getTypeOfProtocolType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSourceUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAdType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTitle
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
        [aParams putKey:@"objectType" withString:@"KalturaAdCuePoint"];
    [aParams addIfDefinedKey:@"protocolType" withString:self.protocolType];
    [aParams addIfDefinedKey:@"sourceUrl" withString:self.sourceUrl];
    [aParams addIfDefinedKey:@"adType" withString:self.adType];
    [aParams addIfDefinedKey:@"title" withString:self.title];
    [aParams addIfDefinedKey:@"endTime" withInt:self.endTime];
}

- (void)dealloc
{
    [self->_protocolType release];
    [self->_sourceUrl release];
    [self->_adType release];
    [self->_title release];
    [super dealloc];
}

@end

@implementation KalturaAdCuePointBaseFilter
@synthesize protocolTypeEqual = _protocolTypeEqual;
@synthesize protocolTypeIn = _protocolTypeIn;
@synthesize titleLike = _titleLike;
@synthesize titleMultiLikeOr = _titleMultiLikeOr;
@synthesize titleMultiLikeAnd = _titleMultiLikeAnd;
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

- (KalturaFieldType)getTypeOfProtocolTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProtocolTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTitleLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTitleMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTitleMultiLikeAnd
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
        [aParams putKey:@"objectType" withString:@"KalturaAdCuePointBaseFilter"];
    [aParams addIfDefinedKey:@"protocolTypeEqual" withString:self.protocolTypeEqual];
    [aParams addIfDefinedKey:@"protocolTypeIn" withString:self.protocolTypeIn];
    [aParams addIfDefinedKey:@"titleLike" withString:self.titleLike];
    [aParams addIfDefinedKey:@"titleMultiLikeOr" withString:self.titleMultiLikeOr];
    [aParams addIfDefinedKey:@"titleMultiLikeAnd" withString:self.titleMultiLikeAnd];
    [aParams addIfDefinedKey:@"endTimeGreaterThanOrEqual" withInt:self.endTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"endTimeLessThanOrEqual" withInt:self.endTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"durationGreaterThanOrEqual" withInt:self.durationGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"durationLessThanOrEqual" withInt:self.durationLessThanOrEqual];
}

- (void)dealloc
{
    [self->_protocolTypeEqual release];
    [self->_protocolTypeIn release];
    [self->_titleLike release];
    [self->_titleMultiLikeOr release];
    [self->_titleMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaAdCuePointFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAdCuePointFilter"];
}

@end

///////////////////////// services /////////////////////////
