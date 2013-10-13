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
@interface KalturaVirusFoundAction : NSObject
+ (int)NONE;
+ (int)DELETE;
+ (int)CLEAN_NONE;
+ (int)CLEAN_DELETE;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanJobResult : NSObject
+ (int)SCAN_ERROR;
+ (int)FILE_IS_CLEAN;
+ (int)FILE_WAS_CLEANED;
+ (int)FILE_INFECTED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanProfileStatus : NSObject
+ (int)DISABLED;
+ (int)ENABLED;
+ (int)DELETED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanEngineType : NSObject
+ (NSString*)CLAMAV_SCAN_ENGINE;
+ (NSString*)SYMANTEC_SCAN_DIRECT_ENGINE;
+ (NSString*)SYMANTEC_SCAN_ENGINE;
+ (NSString*)SYMANTEC_SCAN_JAVA_ENGINE;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanProfileOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaVirusScanProfile : KalturaObjectBase
@property (nonatomic,assign,readonly) int id;
@property (nonatomic,assign,readonly) int createdAt;
@property (nonatomic,assign,readonly) int updatedAt;
@property (nonatomic,assign,readonly) int partnerId;
@property (nonatomic,copy) NSString* name;
@property (nonatomic,assign) int status;	// enum KalturaVirusScanProfileStatus
@property (nonatomic,copy) NSString* engineType;	// enum KalturaVirusScanEngineType
@property (nonatomic,retain) KalturaBaseEntryFilter* entryFilter;
@property (nonatomic,assign) int actionIfInfected;	// enum KalturaVirusFoundAction
- (KalturaFieldType)getTypeOfId;
- (KalturaFieldType)getTypeOfCreatedAt;
- (KalturaFieldType)getTypeOfUpdatedAt;
- (KalturaFieldType)getTypeOfPartnerId;
- (KalturaFieldType)getTypeOfName;
- (KalturaFieldType)getTypeOfStatus;
- (KalturaFieldType)getTypeOfEngineType;
- (KalturaFieldType)getTypeOfEntryFilter;
- (NSString*)getObjectTypeOfEntryFilter;
- (KalturaFieldType)getTypeOfActionIfInfected;
- (void)setIdFromString:(NSString*)aPropVal;
- (void)setCreatedAtFromString:(NSString*)aPropVal;
- (void)setUpdatedAtFromString:(NSString*)aPropVal;
- (void)setPartnerIdFromString:(NSString*)aPropVal;
- (void)setStatusFromString:(NSString*)aPropVal;
- (void)setActionIfInfectedFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanProfileListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaVirusScanProfile elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaParseCaptionAssetJobData : KalturaJobData
@property (nonatomic,copy) NSString* captionAssetId;
- (KalturaFieldType)getTypeOfCaptionAssetId;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanJobData : KalturaJobData
@property (nonatomic,copy) NSString* srcFilePath;
@property (nonatomic,copy) NSString* flavorAssetId;
@property (nonatomic,assign) int scanResult;	// enum KalturaVirusScanJobResult
@property (nonatomic,assign) int virusFoundAction;	// enum KalturaVirusFoundAction
- (KalturaFieldType)getTypeOfSrcFilePath;
- (KalturaFieldType)getTypeOfFlavorAssetId;
- (KalturaFieldType)getTypeOfScanResult;
- (KalturaFieldType)getTypeOfVirusFoundAction;
- (void)setScanResultFromString:(NSString*)aPropVal;
- (void)setVirusFoundActionFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanProfileBaseFilter : KalturaFilter
@property (nonatomic,assign) int idEqual;
@property (nonatomic,copy) NSString* idIn;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
@property (nonatomic,assign) int partnerIdEqual;
@property (nonatomic,copy) NSString* partnerIdIn;
@property (nonatomic,copy) NSString* nameEqual;
@property (nonatomic,copy) NSString* nameLike;
@property (nonatomic,assign) int statusEqual;	// enum KalturaVirusScanProfileStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,copy) NSString* engineTypeEqual;	// enum KalturaVirusScanEngineType
@property (nonatomic,copy) NSString* engineTypeIn;
- (KalturaFieldType)getTypeOfIdEqual;
- (KalturaFieldType)getTypeOfIdIn;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfPartnerIdEqual;
- (KalturaFieldType)getTypeOfPartnerIdIn;
- (KalturaFieldType)getTypeOfNameEqual;
- (KalturaFieldType)getTypeOfNameLike;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfEngineTypeEqual;
- (KalturaFieldType)getTypeOfEngineTypeIn;
- (void)setIdEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setPartnerIdEqualFromString:(NSString*)aPropVal;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaVirusScanProfileFilter : KalturaVirusScanProfileBaseFilter
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// Virus scan profile service
@interface KalturaVirusScanProfileService : KalturaServiceBase
// List virus scan profile objects by filter and pager
- (KalturaVirusScanProfileListResponse*)listWithFilter:(KalturaVirusScanProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaVirusScanProfileListResponse*)listWithFilter:(KalturaVirusScanProfileFilter*)aFilter;
- (KalturaVirusScanProfileListResponse*)list;
// Allows you to add an virus scan profile object and virus scan profile content associated with Kaltura object
- (KalturaVirusScanProfile*)addWithVirusScanProfile:(KalturaVirusScanProfile*)aVirusScanProfile;
// Retrieve an virus scan profile object by id
- (KalturaVirusScanProfile*)getWithVirusScanProfileId:(int)aVirusScanProfileId;
// Update exisitng virus scan profile, it is possible to update the virus scan profile id too
- (KalturaVirusScanProfile*)updateWithVirusScanProfileId:(int)aVirusScanProfileId withVirusScanProfile:(KalturaVirusScanProfile*)aVirusScanProfile;
// Mark the virus scan profile as deleted
- (KalturaVirusScanProfile*)deleteWithVirusScanProfileId:(int)aVirusScanProfileId;
// Scan flavor asset according to virus scan profile
- (int)scanWithFlavorAssetId:(NSString*)aFlavorAssetId withVirusScanProfileId:(int)aVirusScanProfileId;
- (int)scanWithFlavorAssetId:(NSString*)aFlavorAssetId;
@end

@interface KalturaVirusScanClientPlugin : KalturaClientPlugin
{
	KalturaVirusScanProfileService* _virusScanProfile;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaVirusScanProfileService* virusScanProfile;
@end

