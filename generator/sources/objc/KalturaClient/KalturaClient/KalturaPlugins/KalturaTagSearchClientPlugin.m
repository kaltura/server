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
#import "KalturaTagSearchClientPlugin.h"

///////////////////////// enums /////////////////////////
///////////////////////// classes /////////////////////////
@interface KalturaTag()
@property (nonatomic,assign) int id;
@property (nonatomic,copy) NSString* tag;
@property (nonatomic,copy) NSString* taggedObjectType;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int instanceCount;
@property (nonatomic,assign) int createdAt;
@end

@implementation KalturaTag
@synthesize id = _id;
@synthesize tag = _tag;
@synthesize taggedObjectType = _taggedObjectType;
@synthesize partnerId = _partnerId;
@synthesize instanceCount = _instanceCount;
@synthesize createdAt = _createdAt;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_instanceCount = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTag
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTaggedObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInstanceCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInstanceCountFromString:(NSString*)aPropVal
{
    self.instanceCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTag"];
}

- (void)dealloc
{
    [self->_tag release];
    [self->_taggedObjectType release];
    [super dealloc];
}

@end

@interface KalturaTagListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaTagListResponse
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
    return @"KalturaTag";
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
        [aParams putKey:@"objectType" withString:@"KalturaTagListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaTagFilter
@synthesize objectTypeEqual = _objectTypeEqual;
@synthesize tagEqual = _tagEqual;
@synthesize tagStartsWith = _tagStartsWith;
@synthesize instanceCountEqual = _instanceCountEqual;
@synthesize instanceCountIn = _instanceCountIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_instanceCountEqual = KALTURA_UNDEF_INT;
    self->_instanceCountIn = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjectTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTagStartsWith
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfInstanceCountEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfInstanceCountIn
{
    return KFT_Int;
}

- (void)setInstanceCountEqualFromString:(NSString*)aPropVal
{
    self.instanceCountEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setInstanceCountInFromString:(NSString*)aPropVal
{
    self.instanceCountIn = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTagFilter"];
    [aParams addIfDefinedKey:@"objectTypeEqual" withString:self.objectTypeEqual];
    [aParams addIfDefinedKey:@"tagEqual" withString:self.tagEqual];
    [aParams addIfDefinedKey:@"tagStartsWith" withString:self.tagStartsWith];
    [aParams addIfDefinedKey:@"instanceCountEqual" withInt:self.instanceCountEqual];
    [aParams addIfDefinedKey:@"instanceCountIn" withInt:self.instanceCountIn];
}

- (void)dealloc
{
    [self->_objectTypeEqual release];
    [self->_tagEqual release];
    [self->_tagStartsWith release];
    [super dealloc];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaTagService
- (KalturaTagListResponse*)searchWithTagFilter:(KalturaTagFilter*)aTagFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"tagFilter" withObject:aTagFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"tagsearch_tag" withAction:@"search" withExpectedType:@"KalturaTagListResponse"];
}

- (KalturaTagListResponse*)searchWithTagFilter:(KalturaTagFilter*)aTagFilter
{
    return [self searchWithTagFilter:aTagFilter withPager:nil];
}

- (int)deletePending
{
    return [self.client queueIntService:@"tagsearch_tag" withAction:@"deletePending"];
}

@end

@implementation KalturaTagSearchClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaTagService*)tag
{
    if (self->_tag == nil)
    	self->_tag = [[KalturaTagService alloc] initWithClient:self.client];
    return self->_tag;
}

- (void)dealloc
{
    [self->_tag release];
	[super dealloc];
}

@end

