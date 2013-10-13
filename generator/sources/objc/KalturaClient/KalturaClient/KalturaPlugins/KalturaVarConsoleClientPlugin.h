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
///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaVarPartnerUsageItem : KalturaObjectBase
// Partner ID
@property (nonatomic,assign) int partnerId;
// Partner name
@property (nonatomic,copy) NSString* partnerName;
// Partner status
@property (nonatomic,assign) int partnerStatus;	// enum KalturaPartnerStatus
// Partner package
@property (nonatomic,assign) int partnerPackage;
// Partner creation date (Unix timestamp)
@property (nonatomic,assign) int partnerCreatedAt;
// Number of player loads in the specific date range
@property (nonatomic,assign) int views;
// Number of plays in the specific date range
@property (nonatomic,assign) int plays;
// Number of new entries created during specific date range
@property (nonatomic,assign) int entriesCount;
// Total number of entries
@property (nonatomic,assign) int totalEntriesCount;
// Number of new video entries created during specific date range
@property (nonatomic,assign) int videoEntriesCount;
// Number of new image entries created during specific date range
@property (nonatomic,assign) int imageEntriesCount;
// Number of new audio entries created during specific date range
@property (nonatomic,assign) int audioEntriesCount;
// Number of new mix entries created during specific date range
@property (nonatomic,assign) int mixEntriesCount;
// The total bandwidth usage during the given date range (in MB)
@property (nonatomic,assign) double bandwidth;
// The total storage consumption (in MB)
@property (nonatomic,assign) double totalStorage;
// The added storage consumption (new uploads) during the given date range (in MB)
@property (nonatomic,assign) double storage;
// The deleted storage consumption (new uploads) during the given date range (in MB)
@property (nonatomic,assign) double deletedStorage;
// The peak amount of storage consumption during the given date range for the specific publisher
@property (nonatomic,assign) double peakStorage;
// The average amount of storage consumption during the given date range for the specific publisher
@property (nonatomic,assign) double avgStorage;
// The combined amount of bandwidth and storage consumed during the given date range for the specific publisher
@property (nonatomic,assign) double combinedStorageBandwidth;
// TGhe date at which the report was taken - Unix Timestamp
@property (nonatomic,copy) NSString* dateId;
- (KalturaFieldType)getTypeOfPartnerId;
- (KalturaFieldType)getTypeOfPartnerName;
- (KalturaFieldType)getTypeOfPartnerStatus;
- (KalturaFieldType)getTypeOfPartnerPackage;
- (KalturaFieldType)getTypeOfPartnerCreatedAt;
- (KalturaFieldType)getTypeOfViews;
- (KalturaFieldType)getTypeOfPlays;
- (KalturaFieldType)getTypeOfEntriesCount;
- (KalturaFieldType)getTypeOfTotalEntriesCount;
- (KalturaFieldType)getTypeOfVideoEntriesCount;
- (KalturaFieldType)getTypeOfImageEntriesCount;
- (KalturaFieldType)getTypeOfAudioEntriesCount;
- (KalturaFieldType)getTypeOfMixEntriesCount;
- (KalturaFieldType)getTypeOfBandwidth;
- (KalturaFieldType)getTypeOfTotalStorage;
- (KalturaFieldType)getTypeOfStorage;
- (KalturaFieldType)getTypeOfDeletedStorage;
- (KalturaFieldType)getTypeOfPeakStorage;
- (KalturaFieldType)getTypeOfAvgStorage;
- (KalturaFieldType)getTypeOfCombinedStorageBandwidth;
- (KalturaFieldType)getTypeOfDateId;
- (void)setPartnerIdFromString:(NSString*)aPropVal;
- (void)setPartnerStatusFromString:(NSString*)aPropVal;
- (void)setPartnerPackageFromString:(NSString*)aPropVal;
- (void)setPartnerCreatedAtFromString:(NSString*)aPropVal;
- (void)setViewsFromString:(NSString*)aPropVal;
- (void)setPlaysFromString:(NSString*)aPropVal;
- (void)setEntriesCountFromString:(NSString*)aPropVal;
- (void)setTotalEntriesCountFromString:(NSString*)aPropVal;
- (void)setVideoEntriesCountFromString:(NSString*)aPropVal;
- (void)setImageEntriesCountFromString:(NSString*)aPropVal;
- (void)setAudioEntriesCountFromString:(NSString*)aPropVal;
- (void)setMixEntriesCountFromString:(NSString*)aPropVal;
- (void)setBandwidthFromString:(NSString*)aPropVal;
- (void)setTotalStorageFromString:(NSString*)aPropVal;
- (void)setStorageFromString:(NSString*)aPropVal;
- (void)setDeletedStorageFromString:(NSString*)aPropVal;
- (void)setPeakStorageFromString:(NSString*)aPropVal;
- (void)setAvgStorageFromString:(NSString*)aPropVal;
- (void)setCombinedStorageBandwidthFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaPartnerUsageListResponse : KalturaObjectBase
@property (nonatomic,retain) KalturaVarPartnerUsageItem* total;
@property (nonatomic,retain) NSMutableArray* objects;	// of KalturaVarPartnerUsageItem elements
@property (nonatomic,assign) int totalCount;
- (KalturaFieldType)getTypeOfTotal;
- (NSString*)getObjectTypeOfTotal;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVarPartnerUsageTotalItem : KalturaVarPartnerUsageItem
@end

// @package External
// @subpackage Kaltura
@interface KalturaVarConsolePartnerFilter : KalturaPartnerFilter
// Eq filter for the partner's group type
@property (nonatomic,assign) int groupTypeEq;	// enum KalturaPartnerGroupType
// In filter for the partner's group type
@property (nonatomic,copy) NSString* groupTypeIn;
// Filter for partner permissions- filter contains comma-separated string of permission names which the returned partners should have.
@property (nonatomic,copy) NSString* partnerPermissionsExist;
- (KalturaFieldType)getTypeOfGroupTypeEq;
- (KalturaFieldType)getTypeOfGroupTypeIn;
- (KalturaFieldType)getTypeOfPartnerPermissionsExist;
- (void)setGroupTypeEqFromString:(NSString*)aPropVal;
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// Utility service for the Multi-publishers console
@interface KalturaVarConsoleService : KalturaServiceBase
// Function which calulates partner usage of a group of a VAR's sub-publishers
- (KalturaPartnerUsageListResponse*)getPartnerUsageWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter withUsageFilter:(KalturaReportInputFilter*)aUsageFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaPartnerUsageListResponse*)getPartnerUsageWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter withUsageFilter:(KalturaReportInputFilter*)aUsageFilter;
- (KalturaPartnerUsageListResponse*)getPartnerUsageWithPartnerFilter:(KalturaPartnerFilter*)aPartnerFilter;
- (KalturaPartnerUsageListResponse*)getPartnerUsage;
// Function to change a sub-publisher's status
- (void)updateStatusWithId:(int)aId withStatus:(int)aStatus;
@end

@interface KalturaVarConsoleClientPlugin : KalturaClientPlugin
{
	KalturaVarConsoleService* _varConsole;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaVarConsoleService* varConsole;
@end

