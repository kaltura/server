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
#import "KalturaVarConsoleClientPlugin.h"

///////////////////////// enums /////////////////////////
///////////////////////// classes /////////////////////////
@implementation KalturaVarPartnerUsageItem
@synthesize partnerId = _partnerId;
@synthesize partnerName = _partnerName;
@synthesize partnerStatus = _partnerStatus;
@synthesize partnerPackage = _partnerPackage;
@synthesize partnerCreatedAt = _partnerCreatedAt;
@synthesize views = _views;
@synthesize plays = _plays;
@synthesize entriesCount = _entriesCount;
@synthesize totalEntriesCount = _totalEntriesCount;
@synthesize videoEntriesCount = _videoEntriesCount;
@synthesize imageEntriesCount = _imageEntriesCount;
@synthesize audioEntriesCount = _audioEntriesCount;
@synthesize mixEntriesCount = _mixEntriesCount;
@synthesize bandwidth = _bandwidth;
@synthesize totalStorage = _totalStorage;
@synthesize storage = _storage;
@synthesize deletedStorage = _deletedStorage;
@synthesize peakStorage = _peakStorage;
@synthesize avgStorage = _avgStorage;
@synthesize combinedStorageBandwidth = _combinedStorageBandwidth;
@synthesize dateId = _dateId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_partnerStatus = KALTURA_UNDEF_INT;
    self->_partnerPackage = KALTURA_UNDEF_INT;
    self->_partnerCreatedAt = KALTURA_UNDEF_INT;
    self->_views = KALTURA_UNDEF_INT;
    self->_plays = KALTURA_UNDEF_INT;
    self->_entriesCount = KALTURA_UNDEF_INT;
    self->_totalEntriesCount = KALTURA_UNDEF_INT;
    self->_videoEntriesCount = KALTURA_UNDEF_INT;
    self->_imageEntriesCount = KALTURA_UNDEF_INT;
    self->_audioEntriesCount = KALTURA_UNDEF_INT;
    self->_mixEntriesCount = KALTURA_UNDEF_INT;
    self->_bandwidth = KALTURA_UNDEF_FLOAT;
    self->_totalStorage = KALTURA_UNDEF_FLOAT;
    self->_storage = KALTURA_UNDEF_FLOAT;
    self->_deletedStorage = KALTURA_UNDEF_FLOAT;
    self->_peakStorage = KALTURA_UNDEF_FLOAT;
    self->_avgStorage = KALTURA_UNDEF_FLOAT;
    self->_combinedStorageBandwidth = KALTURA_UNDEF_FLOAT;
    return self;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerPackage
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfViews
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPlays
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfTotalEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVideoEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfImageEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAudioEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMixEntriesCount
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfBandwidth
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfTotalStorage
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfStorage
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfDeletedStorage
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfPeakStorage
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfAvgStorage
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfCombinedStorageBandwidth
{
    return KFT_Float;
}

- (KalturaFieldType)getTypeOfDateId
{
    return KFT_String;
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerStatusFromString:(NSString*)aPropVal
{
    self.partnerStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerPackageFromString:(NSString*)aPropVal
{
    self.partnerPackage = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerCreatedAtFromString:(NSString*)aPropVal
{
    self.partnerCreatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setViewsFromString:(NSString*)aPropVal
{
    self.views = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPlaysFromString:(NSString*)aPropVal
{
    self.plays = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEntriesCountFromString:(NSString*)aPropVal
{
    self.entriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setTotalEntriesCountFromString:(NSString*)aPropVal
{
    self.totalEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVideoEntriesCountFromString:(NSString*)aPropVal
{
    self.videoEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setImageEntriesCountFromString:(NSString*)aPropVal
{
    self.imageEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setAudioEntriesCountFromString:(NSString*)aPropVal
{
    self.audioEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMixEntriesCountFromString:(NSString*)aPropVal
{
    self.mixEntriesCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setBandwidthFromString:(NSString*)aPropVal
{
    self.bandwidth = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setTotalStorageFromString:(NSString*)aPropVal
{
    self.totalStorage = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setStorageFromString:(NSString*)aPropVal
{
    self.storage = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setDeletedStorageFromString:(NSString*)aPropVal
{
    self.deletedStorage = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setPeakStorageFromString:(NSString*)aPropVal
{
    self.peakStorage = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setAvgStorageFromString:(NSString*)aPropVal
{
    self.avgStorage = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)setCombinedStorageBandwidthFromString:(NSString*)aPropVal
{
    self.combinedStorageBandwidth = [KalturaSimpleTypeParser parseFloat:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVarPartnerUsageItem"];
    [aParams addIfDefinedKey:@"partnerId" withInt:self.partnerId];
    [aParams addIfDefinedKey:@"partnerName" withString:self.partnerName];
    [aParams addIfDefinedKey:@"partnerStatus" withInt:self.partnerStatus];
    [aParams addIfDefinedKey:@"partnerPackage" withInt:self.partnerPackage];
    [aParams addIfDefinedKey:@"partnerCreatedAt" withInt:self.partnerCreatedAt];
    [aParams addIfDefinedKey:@"views" withInt:self.views];
    [aParams addIfDefinedKey:@"plays" withInt:self.plays];
    [aParams addIfDefinedKey:@"entriesCount" withInt:self.entriesCount];
    [aParams addIfDefinedKey:@"totalEntriesCount" withInt:self.totalEntriesCount];
    [aParams addIfDefinedKey:@"videoEntriesCount" withInt:self.videoEntriesCount];
    [aParams addIfDefinedKey:@"imageEntriesCount" withInt:self.imageEntriesCount];
    [aParams addIfDefinedKey:@"audioEntriesCount" withInt:self.audioEntriesCount];
    [aParams addIfDefinedKey:@"mixEntriesCount" withInt:self.mixEntriesCount];
    [aParams addIfDefinedKey:@"bandwidth" withFloat:self.bandwidth];
    [aParams addIfDefinedKey:@"totalStorage" withFloat:self.totalStorage];
    [aParams addIfDefinedKey:@"storage" withFloat:self.storage];
    [aParams addIfDefinedKey:@"deletedStorage" withFloat:self.deletedStorage];
    [aParams addIfDefinedKey:@"peakStorage" withFloat:self.peakStorage];
    [aParams addIfDefinedKey:@"avgStorage" withFloat:self.avgStorage];
    [aParams addIfDefinedKey:@"combinedStorageBandwidth" withFloat:self.combinedStorageBandwidth];
    [aParams addIfDefinedKey:@"dateId" withString:self.dateId];
}

- (void)dealloc
{
    [self->_partnerName release];
    [self->_dateId release];
    [super dealloc];
}

@end

@implementation KalturaPartnerUsageListResponse
@synthesize total = _total;
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

- (KalturaFieldType)getTypeOfTotal
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfTotal
{
    return @"KalturaVarPartnerUsageItem";
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaVarPartnerUsageItem";
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
        [aParams putKey:@"objectType" withString:@"KalturaPartnerUsageListResponse"];
    [aParams addIfDefinedKey:@"total" withObject:self.total];
    [aParams addIfDefinedKey:@"objects" withArray:self.objects];
    [aParams addIfDefinedKey:@"totalCount" withInt:self.totalCount];
}

- (void)dealloc
{
    [self->_total release];
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaVarPartnerUsageTotalItem
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVarPartnerUsageTotalItem"];
}

@end

@implementation KalturaVarConsolePartnerFilter
@synthesize groupTypeEq = _groupTypeEq;
@synthesize groupTypeIn = _groupTypeIn;
@synthesize partnerPermissionsExist = _partnerPermissionsExist;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_groupTypeEq = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfGroupTypeEq
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGroupTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerPermissionsExist
{
    return KFT_String;
}

- (void)setGroupTypeEqFromString:(NSString*)aPropVal
{
    self.groupTypeEq = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVarConsolePartnerFilter"];
    [aParams addIfDefinedKey:@"groupTypeEq" withInt:self.groupTypeEq];
    [aParams addIfDefinedKey:@"groupTypeIn" withString:self.groupTypeIn];
    [aParams addIfDefinedKey:@"partnerPermissionsExist" withString:self.partnerPermissionsExist];
}

- (void)dealloc
{
    [self->_groupTypeIn release];
    [self->_partnerPermissionsExist release];
    [super dealloc];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaVarConsoleService
- (KalturaPartnerUsageListResponse*)getPartnerUsageWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter withUsageFilter:(KalturaReportInputFilter*)aUsageFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"partnerFilter" withObject:aPartnerFilter];
    [self.client.params addIfDefinedKey:@"usageFilter" withObject:aUsageFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"varconsole_varconsole" withAction:@"getPartnerUsage" withExpectedType:@"KalturaPartnerUsageListResponse"];
}

- (KalturaPartnerUsageListResponse*)getPartnerUsageWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter withUsageFilter:(KalturaReportInputFilter*)aUsageFilter
{
    return [self getPartnerUsageWithPartnerFilter:aPartnerFilter withUsageFilter:aUsageFilter withPager:nil];
}

- (KalturaPartnerUsageListResponse*)getPartnerUsageWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter
{
    return [self getPartnerUsageWithPartnerFilter:aPartnerFilter withUsageFilter:nil];
}

- (KalturaPartnerUsageListResponse*)getPartnerUsage
{
    return [self getPartnerUsageWithPartnerFilter:nil];
}

- (void)updateStatusWithId:(int)aId withStatus:(int)aStatus
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"status" withInt:aStatus];
    [self.client queueVoidService:@"varconsole_varconsole" withAction:@"updateStatus"];
}

@end

@implementation KalturaVarConsoleClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaVarConsoleService*)varConsole
{
    if (self->_varConsole == nil)
    	self->_varConsole = [[KalturaVarConsoleService alloc] initWithClient:self.client];
    return self->_varConsole;
}

- (void)dealloc
{
    [self->_varConsole release];
	[super dealloc];
}

@end

