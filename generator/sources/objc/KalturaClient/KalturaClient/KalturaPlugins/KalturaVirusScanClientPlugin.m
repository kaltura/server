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
#import "KalturaVirusScanClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaVirusFoundAction
+ (int)NONE
{
    return 0;
}
+ (int)DELETE
{
    return 1;
}
+ (int)CLEAN_NONE
{
    return 2;
}
+ (int)CLEAN_DELETE
{
    return 3;
}
@end

@implementation KalturaVirusScanJobResult
+ (int)SCAN_ERROR
{
    return 1;
}
+ (int)FILE_IS_CLEAN
{
    return 2;
}
+ (int)FILE_WAS_CLEANED
{
    return 3;
}
+ (int)FILE_INFECTED
{
    return 4;
}
@end

@implementation KalturaVirusScanProfileStatus
+ (int)DISABLED
{
    return 1;
}
+ (int)ENABLED
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
@end

@implementation KalturaVirusScanEngineType
+ (NSString*)CLAMAV_SCAN_ENGINE
{
    return @"clamAVScanEngine.ClamAV";
}
+ (NSString*)SYMANTEC_SCAN_DIRECT_ENGINE
{
    return @"symantecScanEngine.SymantecScanDirectEngine";
}
+ (NSString*)SYMANTEC_SCAN_ENGINE
{
    return @"symantecScanEngine.SymantecScanEngine";
}
+ (NSString*)SYMANTEC_SCAN_JAVA_ENGINE
{
    return @"symantecScanEngine.SymantecScanJavaEngine";
}
@end

@implementation KalturaVirusScanProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

///////////////////////// classes /////////////////////////
@interface KalturaVirusScanProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int partnerId;
@end

@implementation KalturaVirusScanProfile
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerId = _partnerId;
@synthesize name = _name;
@synthesize status = _status;
@synthesize engineType = _engineType;
@synthesize entryFilter = _entryFilter;
@synthesize actionIfInfected = _actionIfInfected;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_actionIfInfected = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEngineType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfEntryFilter
{
    return @"KalturaBaseEntryFilter";
}

- (KalturaFieldType)getTypeOfActionIfInfected
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setActionIfInfectedFromString:(NSString*)aPropVal
{
    self.actionIfInfected = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVirusScanProfile"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"engineType" withString:self.engineType];
    [aParams addIfDefinedKey:@"entryFilter" withObject:self.entryFilter];
    [aParams addIfDefinedKey:@"actionIfInfected" withInt:self.actionIfInfected];
}

- (void)dealloc
{
    [self->_name release];
    [self->_engineType release];
    [self->_entryFilter release];
    [super dealloc];
}

@end

@interface KalturaVirusScanProfileListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaVirusScanProfileListResponse
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
    return @"KalturaVirusScanProfile";
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
        [aParams putKey:@"objectType" withString:@"KalturaVirusScanProfileListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaParseCaptionAssetJobData
@synthesize captionAssetId = _captionAssetId;

- (KalturaFieldType)getTypeOfCaptionAssetId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaParseCaptionAssetJobData"];
    [aParams addIfDefinedKey:@"captionAssetId" withString:self.captionAssetId];
}

- (void)dealloc
{
    [self->_captionAssetId release];
    [super dealloc];
}

@end

@implementation KalturaVirusScanJobData
@synthesize srcFilePath = _srcFilePath;
@synthesize flavorAssetId = _flavorAssetId;
@synthesize scanResult = _scanResult;
@synthesize virusFoundAction = _virusFoundAction;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_scanResult = KALTURA_UNDEF_INT;
    self->_virusFoundAction = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSrcFilePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfScanResult
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVirusFoundAction
{
    return KFT_Int;
}

- (void)setScanResultFromString:(NSString*)aPropVal
{
    self.scanResult = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVirusFoundActionFromString:(NSString*)aPropVal
{
    self.virusFoundAction = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVirusScanJobData"];
    [aParams addIfDefinedKey:@"srcFilePath" withString:self.srcFilePath];
    [aParams addIfDefinedKey:@"flavorAssetId" withString:self.flavorAssetId];
    [aParams addIfDefinedKey:@"scanResult" withInt:self.scanResult];
    [aParams addIfDefinedKey:@"virusFoundAction" withInt:self.virusFoundAction];
}

- (void)dealloc
{
    [self->_srcFilePath release];
    [self->_flavorAssetId release];
    [super dealloc];
}

@end

@implementation KalturaVirusScanProfileBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize nameEqual = _nameEqual;
@synthesize nameLike = _nameLike;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize engineTypeEqual = _engineTypeEqual;
@synthesize engineTypeIn = _engineTypeIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNameLike
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEngineTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEngineTypeIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVirusScanProfileBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"nameLike" withString:self.nameLike];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"engineTypeEqual" withString:self.engineTypeEqual];
    [aParams addIfDefinedKey:@"engineTypeIn" withString:self.engineTypeIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_partnerIdIn release];
    [self->_nameEqual release];
    [self->_nameLike release];
    [self->_statusIn release];
    [self->_engineTypeEqual release];
    [self->_engineTypeIn release];
    [super dealloc];
}

@end

@implementation KalturaVirusScanProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaVirusScanProfileFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaVirusScanProfileService
- (KalturaVirusScanProfileListResponse*)listWithFilter:(KalturaVirusScanProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"virusscan_virusscanprofile" withAction:@"list" withExpectedType:@"KalturaVirusScanProfileListResponse"];
}

- (KalturaVirusScanProfileListResponse*)listWithFilter:(KalturaVirusScanProfileFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaVirusScanProfileListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaVirusScanProfile*)addWithVirusScanProfile:(KalturaVirusScanProfile*)aVirusScanProfile
{
    [self.client.params addIfDefinedKey:@"virusScanProfile" withObject:aVirusScanProfile];
    return [self.client queueObjectService:@"virusscan_virusscanprofile" withAction:@"add" withExpectedType:@"KalturaVirusScanProfile"];
}

- (KalturaVirusScanProfile*)getWithVirusScanProfileId:(int)aVirusScanProfileId
{
    [self.client.params addIfDefinedKey:@"virusScanProfileId" withInt:aVirusScanProfileId];
    return [self.client queueObjectService:@"virusscan_virusscanprofile" withAction:@"get" withExpectedType:@"KalturaVirusScanProfile"];
}

- (KalturaVirusScanProfile*)updateWithVirusScanProfileId:(int)aVirusScanProfileId withVirusScanProfile:(KalturaVirusScanProfile*)aVirusScanProfile
{
    [self.client.params addIfDefinedKey:@"virusScanProfileId" withInt:aVirusScanProfileId];
    [self.client.params addIfDefinedKey:@"virusScanProfile" withObject:aVirusScanProfile];
    return [self.client queueObjectService:@"virusscan_virusscanprofile" withAction:@"update" withExpectedType:@"KalturaVirusScanProfile"];
}

- (KalturaVirusScanProfile*)deleteWithVirusScanProfileId:(int)aVirusScanProfileId
{
    [self.client.params addIfDefinedKey:@"virusScanProfileId" withInt:aVirusScanProfileId];
    return [self.client queueObjectService:@"virusscan_virusscanprofile" withAction:@"delete" withExpectedType:@"KalturaVirusScanProfile"];
}

- (int)scanWithFlavorAssetId:(NSString*)aFlavorAssetId withVirusScanProfileId:(int)aVirusScanProfileId
{
    [self.client.params addIfDefinedKey:@"flavorAssetId" withString:aFlavorAssetId];
    [self.client.params addIfDefinedKey:@"virusScanProfileId" withInt:aVirusScanProfileId];
    return [self.client queueIntService:@"virusscan_virusscanprofile" withAction:@"scan"];
}

- (int)scanWithFlavorAssetId:(NSString*)aFlavorAssetId
{
    return [self scanWithFlavorAssetId:aFlavorAssetId withVirusScanProfileId:KALTURA_UNDEF_INT];
}

@end

@implementation KalturaVirusScanClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaVirusScanProfileService*)virusScanProfile
{
    if (self->_virusScanProfile == nil)
    	self->_virusScanProfile = [[KalturaVirusScanProfileService alloc] initWithClient:self.client];
    return self->_virusScanProfile;
}

- (void)dealloc
{
    [self->_virusScanProfile release];
	[super dealloc];
}

@end

