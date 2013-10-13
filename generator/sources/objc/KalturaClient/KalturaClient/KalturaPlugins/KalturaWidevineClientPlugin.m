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
#import "KalturaWidevineClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaWidevineFlavorAssetOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)DELETED_AT_ASC
{
    return @"+deletedAt";
}
+ (NSString*)SIZE_ASC
{
    return @"+size";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)DELETED_AT_DESC
{
    return @"-deletedAt";
}
+ (NSString*)SIZE_DESC
{
    return @"-size";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaWidevineFlavorParamsOrderBy
@end

@implementation KalturaWidevineFlavorParamsOutputOrderBy
@end

///////////////////////// classes /////////////////////////
@implementation KalturaWidevineFlavorAsset
@synthesize widevineDistributionStartDate = _widevineDistributionStartDate;
@synthesize widevineDistributionEndDate = _widevineDistributionEndDate;
@synthesize widevineAssetId = _widevineAssetId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_widevineDistributionStartDate = KALTURA_UNDEF_INT;
    self->_widevineDistributionEndDate = KALTURA_UNDEF_INT;
    self->_widevineAssetId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfWidevineDistributionStartDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidevineDistributionEndDate
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfWidevineAssetId
{
    return KFT_Int;
}

- (void)setWidevineDistributionStartDateFromString:(NSString*)aPropVal
{
    self.widevineDistributionStartDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidevineDistributionEndDateFromString:(NSString*)aPropVal
{
    self.widevineDistributionEndDate = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setWidevineAssetIdFromString:(NSString*)aPropVal
{
    self.widevineAssetId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorAsset"];
    [aParams addIfDefinedKey:@"widevineDistributionStartDate" withInt:self.widevineDistributionStartDate];
    [aParams addIfDefinedKey:@"widevineDistributionEndDate" withInt:self.widevineDistributionEndDate];
    [aParams addIfDefinedKey:@"widevineAssetId" withInt:self.widevineAssetId];
}

@end

@implementation KalturaWidevineFlavorParams
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorParams"];
}

@end

@implementation KalturaWidevineFlavorParamsOutput
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorParamsOutput"];
}

@end

@implementation KalturaWidevineFlavorAssetBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorAssetBaseFilter"];
}

@end

@implementation KalturaWidevineFlavorParamsBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorParamsBaseFilter"];
}

@end

@implementation KalturaWidevineFlavorAssetFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorAssetFilter"];
}

@end

@implementation KalturaWidevineFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorParamsFilter"];
}

@end

@implementation KalturaWidevineFlavorParamsOutputBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorParamsOutputBaseFilter"];
}

@end

@implementation KalturaWidevineFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaWidevineFlavorParamsOutputFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaWidevineDrmService
- (NSString*)getLicenseWithFlavorAssetId:(NSString*)aFlavorAssetId
{
    [self.client.params addIfDefinedKey:@"flavorAssetId" withString:aFlavorAssetId];
    return [self.client queueStringService:@"widevine_widevinedrm" withAction:@"getLicense"];
}

@end

@implementation KalturaWidevineClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaWidevineDrmService*)widevineDrm
{
    if (self->_widevineDrm == nil)
    	self->_widevineDrm = [[KalturaWidevineDrmService alloc] initWithClient:self.client];
    return self->_widevineDrm;
}

- (void)dealloc
{
    [self->_widevineDrm release];
	[super dealloc];
}

@end

