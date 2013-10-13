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
#import "KalturaAnnotationClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaAnnotationOrderBy
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
@interface KalturaAnnotation()
@property (nonatomic,assign) int duration;
@property (nonatomic,assign) int depth;
@property (nonatomic,assign) int childrenCount;
@property (nonatomic,assign) int directChildrenCount;
@end

@implementation KalturaAnnotation
@synthesize parentId = _parentId;
@synthesize text = _text;
@synthesize endTime = _endTime;
@synthesize duration = _duration;
@synthesize depth = _depth;
@synthesize childrenCount = _childrenCount;
@synthesize directChildrenCount = _directChildrenCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_endTime = KALTURA_UNDEF_INT;
    self->_duration = KALTURA_UNDEF_INT;
    self->_depth = KALTURA_UNDEF_INT;
    self->_childrenCount = KALTURA_UNDEF_INT;
    self->_directChildrenCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfParentId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfText
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

- (KalturaFieldType)getTypeOfDepth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfChildrenCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDirectChildrenCount
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

- (void)setDepthFromString:(NSString*)aPropVal
{
    self.depth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setChildrenCountFromString:(NSString*)aPropVal
{
    self.childrenCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDirectChildrenCountFromString:(NSString*)aPropVal
{
    self.directChildrenCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAnnotation"];
    [aParams addIfDefinedKey:@"parentId" withString:self.parentId];
    [aParams addIfDefinedKey:@"text" withString:self.text];
    [aParams addIfDefinedKey:@"endTime" withInt:self.endTime];
}

- (void)dealloc
{
    [self->_parentId release];
    [self->_text release];
    [super dealloc];
}

@end

@interface KalturaAnnotationListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaAnnotationListResponse
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
    return @"KalturaAnnotation";
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
        [aParams putKey:@"objectType" withString:@"KalturaAnnotationListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaAnnotationBaseFilter
@synthesize parentIdEqual = _parentIdEqual;
@synthesize parentIdIn = _parentIdIn;
@synthesize textLike = _textLike;
@synthesize textMultiLikeOr = _textMultiLikeOr;
@synthesize textMultiLikeAnd = _textMultiLikeAnd;
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

- (KalturaFieldType)getTypeOfParentIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfParentIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTextLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTextMultiLikeOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTextMultiLikeAnd
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
        [aParams putKey:@"objectType" withString:@"KalturaAnnotationBaseFilter"];
    [aParams addIfDefinedKey:@"parentIdEqual" withString:self.parentIdEqual];
    [aParams addIfDefinedKey:@"parentIdIn" withString:self.parentIdIn];
    [aParams addIfDefinedKey:@"textLike" withString:self.textLike];
    [aParams addIfDefinedKey:@"textMultiLikeOr" withString:self.textMultiLikeOr];
    [aParams addIfDefinedKey:@"textMultiLikeAnd" withString:self.textMultiLikeAnd];
    [aParams addIfDefinedKey:@"endTimeGreaterThanOrEqual" withInt:self.endTimeGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"endTimeLessThanOrEqual" withInt:self.endTimeLessThanOrEqual];
    [aParams addIfDefinedKey:@"durationGreaterThanOrEqual" withInt:self.durationGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"durationLessThanOrEqual" withInt:self.durationLessThanOrEqual];
}

- (void)dealloc
{
    [self->_parentIdEqual release];
    [self->_parentIdIn release];
    [self->_textLike release];
    [self->_textMultiLikeOr release];
    [self->_textMultiLikeAnd release];
    [super dealloc];
}

@end

@implementation KalturaAnnotationFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAnnotationFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaAnnotationService
- (KalturaAnnotation*)addWithAnnotation:(KalturaCuePoint*)aAnnotation
{
    [self.client.params addIfDefinedKey:@"annotation" withObject:aAnnotation];
    return [self.client queueObjectService:@"annotation_annotation" withAction:@"add" withExpectedType:@"KalturaAnnotation"];
}

- (KalturaAnnotation*)updateWithId:(NSString*)aId withAnnotation:(KalturaCuePoint*)aAnnotation
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client.params addIfDefinedKey:@"annotation" withObject:aAnnotation];
    return [self.client queueObjectService:@"annotation_annotation" withAction:@"update" withExpectedType:@"KalturaAnnotation"];
}

- (KalturaAnnotationListResponse*)listWithFilter:(KalturaCuePointFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"annotation_annotation" withAction:@"list" withExpectedType:@"KalturaAnnotationListResponse"];
}

- (KalturaAnnotationListResponse*)listWithFilter:(KalturaCuePointFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaAnnotationListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaCuePointListResponse*)addFromBulkWithFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueObjectService:@"annotation_annotation" withAction:@"addFromBulk" withExpectedType:@"KalturaCuePointListResponse"];
}

- (NSString*)serveBulkWithFilter:(KalturaCuePointFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueServeService:@"annotation_annotation" withAction:@"serveBulk"];
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
    return [self.client queueObjectService:@"annotation_annotation" withAction:@"get" withExpectedType:@"KalturaCuePoint"];
}

- (int)countWithFilter:(KalturaCuePointFilter*)aFilter
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    return [self.client queueIntService:@"annotation_annotation" withAction:@"count"];
}

- (int)count
{
    return [self countWithFilter:nil];
}

- (void)deleteWithId:(NSString*)aId
{
    [self.client.params addIfDefinedKey:@"id" withString:aId];
    [self.client queueVoidService:@"annotation_annotation" withAction:@"delete"];
}

@end

@implementation KalturaAnnotationClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaAnnotationService*)annotation
{
    if (self->_annotation == nil)
    	self->_annotation = [[KalturaAnnotationService alloc] initWithClient:self.client];
    return self->_annotation;
}

- (void)dealloc
{
    [self->_annotation release];
	[super dealloc];
}

@end

